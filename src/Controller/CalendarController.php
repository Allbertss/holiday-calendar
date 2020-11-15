<?php

namespace App\Controller;

use App\Entity\HolidayQuery;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class CalendarController extends AbstractController {
    private $client;
    private $content;
    private $contries = [];
    private $regions = [];
    private $codes = []; // country codes

    private $selectedCountry;
    private $seletedIndex;

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;

        $response = $this->client->request('GET', 'https://kayaposoft.com/enrico/json/v2.0/?action=getSupportedCountries');
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200)
            throw new \Exception('Error: can\' get supported countries.');
        
        $this->content = $response->getContent();
        $this->content = $response->toArray();

        for ($i = 0; $i < sizeof($this->content); $i++) {
            $this->countries[$this->content[$i]['fullName']] = $i;
            $this->codes[$this->content[$i]['countryCode']] = $i;
        }
    }

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request) {
        $form = $this->createFormBuilder()
            ->add('SelectCountry', ChoiceType::class, [
                'choices' => $this->countries])
            ->add('save', SubmitType::class, [
                'label' => 'Select',
                'attr' => ['class' => 'btn btn-primary mt-3']])
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
            return $this->redirectToRoute('select', ['code' => $this->content[$form->getData()['SelectCountry']]['countryCode']]);

        return $this->render('index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/select/{code?}", name="select")
    */
    public function select(Request $request) {
        $code = $request->get('code'); // get selected country code
        if (!$code) {
            return $this->redirectToRoute('home');
        } else {
            $index = $this->codes[$code];
            $numberOfRegions = sizeof($this->content[$index]['regions']); // selected country's number of regions
            if ($numberOfRegions > 0) { // selected country has regions
                for ($i = 0; $i < $numberOfRegions; $i++)
                    $this->regions[$this->content[$index]['regions'][$i]] = $i;
    
                $form = $this->createFormBuilder()
                    ->add('SelectRegion', ChoiceType::class, [
                        'choices' => $this->regions])
                    ->add('FromDate', DateType::class, [
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd',
                        'data' => new \DateTime("now")])
                    ->add('ToDate', DateType::class, [
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd',
                        'data' => new \DateTime("now")])
                    ->add('save', SubmitType::class, [
                        'label' => 'Select',
                        'attr' => ['class' => 'btn btn-primary mt-3']])
                    ->getForm();
    
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();
                    $fullName = $this->content[$index]['fullName'];
                    $countryCode = $this->content[$index]['countryCode'];
                    $region = $this->content[$index]['regions'][$data['SelectRegion']];

                    // trash code
                    preg_match('/\d{4}-\d{2}-\d{2}/', json_encode($data['FromDate']), $fromDate);
                    preg_match('/\d{4}-\d{2}-\d{2}/', json_encode($data['ToDate']), $toDate);
    
                    $fromYear = substr($fromDate[0], 0, 4);
                    $fromMonth = substr($fromDate[0], 5, 2);
                    $fromDay = substr($fromDate[0], 8, 2);
                    $fromDateCompare = $fromYear . '-' . $fromMonth . '-' . $fromDay;
                    $fromDateNormalized = $fromDay . '-' . $fromMonth . '-' . $fromYear;
    
                    $toYear = substr($toDate[0], 0, 4);
                    $toMonth = substr($toDate[0], 5, 2);
                    $toDay = substr($toDate[0], 8, 2);
                    $toDateCompare = $toYear . '-' .$toMonth . '-' . $toDay;
                    $toDateNormalized = $toDay . '-' . $toMonth . '-' . $toYear;

                    if ($fromDateCompare > $toDateCompare) {
                        $temp = $fromDateNormalized;
                        $fromDateNormalized = $toDateNormalized;
                        $toDateNormalized = $temp;
                    }
    
                    return $this->redirectToRoute('show', [
                        'fromDate' => $fromDateNormalized,
                        'toDate' => $toDateNormalized,
                        'code' => $countryCode,
                        'region' => $region]);
                }
            } else { // selected country has no regions
                $form = $this->createFormBuilder()
                    ->add('FromDate', DateType::class, [
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd',
                        'data' => new \DateTime("now")])
                    ->add('ToDate', DateType::class, [
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd',
                        'data' => new \DateTime("now")])
                    ->add('save', SubmitType::class, [
                        'label' => 'Select',
                        'attr' => ['class' => 'btn btn-primary mt-3']])
                    ->getForm();
    
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();
                    $fullName = $this->content[$index]['fullName'];
                    $countryCode = $this->content[$index]['countryCode'];

                    // trash code
                    preg_match('/\d{4}-\d{2}-\d{2}/', json_encode($data['FromDate']), $fromDate);
                    preg_match('/\d{4}-\d{2}-\d{2}/', json_encode($data['ToDate']), $toDate);
    
                    $fromYear = substr($fromDate[0], 0, 4);
                    $fromMonth = substr($fromDate[0], 5, 2);
                    $fromDay = substr($fromDate[0], 8, 2);
                    $fromDateCompare = $fromYear . '-' . $fromMonth . '-' . $fromDay;
                    $fromDateNormalized = $fromDay . '-' . $fromMonth . '-' . $fromYear;
    
                    $toYear = substr($toDate[0], 0, 4);
                    $toMonth = substr($toDate[0], 5, 2);
                    $toDay = substr($toDate[0], 8, 2);
                    $toDateCompare = $toYear . '-' .$toMonth . '-' . $toDay;
                    $toDateNormalized = $toDay . '-' . $toMonth . '-' . $toYear;

                    if ($fromDateCompare > $toDateCompare) {
                        $temp = $fromDateNormalized;
                        $fromDateNormalized = $toDateNormalized;
                        $toDateNormalized = $temp;
                    }

                    return $this->redirectToRoute('show', [
                        'fromDate' => $fromDateNormalized,
                        'toDate' => $toDateNormalized,
                        'code' => $countryCode]);
                }
            }
        }

        return $this->render('index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/show/{fromDate}/{toDate}/{code}/{region?}", name="show")
    */
    public function show(Request $request) {
        $url = '';
        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');
        $countryCode = $request->get('code');
        $region = $request->get('region');

        // was queried before
        $data = $this->getDoctrine()->getRepository(HolidayQuery::class)->findAll();
        $foundInDatabase = -1;
        if (!$region)
            for ($i = 0; $i < sizeof($data); $i++)
                if ($data[$i]->getFromDate() == $fromDate && $data[$i]->getToDate() == $toDate && $data[$i]->getCountryCode() == $countryCode && $data[$i]->getRegion() == $region)
                    $foundInDatabase = $i;
        else
            for ($i = 0; $i < sizeof($data); $i++)
                if ($data[$i]->getFromDate() == $fromDate && $data[$i]->getToDate() == $toDate && $data[$i]->getCountryCode() == $countryCode)
                    $foundInDatabase = $i;

        if ($foundInDatabase == -1) { // new query
            if (!$region)
                $url = 'https://kayaposoft.com/enrico/json/v2.0/?action=getHolidaysForDateRange&fromDate=' . $fromDate . '&toDate=' . $toDate .'&country=' . $countryCode . '&holidayType=public_holiday';
            else
                $url = 'https://kayaposoft.com/enrico/json/v2.0/?action=getHolidaysForDateRange&fromDate=' . $fromDate . '&toDate=' . $toDate .'&country=' . $countryCode . '&region=' . $region . '&holidayType=public_holiday';
        
            $response = $this->client->request('GET', $url);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200)
                throw new \Exception('Error: can\'t get holidays for data range.');
    
            $result = $response->getContent();
            $result = $response->toArray(); // data from enrico

            // delete texts which are not in english
            for ($i = 0; $i < sizeof($result); $i++) {
                $len = sizeof($result[$i]['name']);
                for ($j = 0; $j < $len; $j++)
                    if ($result[$i]['name'][$j]['lang'] !== "en")
                        unset($result[$i]['name'][$j]);
            }

            $result = rekeyMultiArray($result);

            // add data to database
            $entityManager = $this->getDoctrine()->getManager();
            $hq = new HolidayQuery();
            $hq->setFromDate($fromDate);
            $hq->setToDate($toDate);
            $hq->setCountryCode($countryCode);
            if ($region)
                $hq->setRegion($region);

            $hq->setResult($result);

            $entityManager->persist($hq);
            $entityManager->flush();
        } else { // get data from database
            if (!$region) {
                $result = $this->getDoctrine()->getRepository(HolidayQuery::class)->findOneBy([
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'country_code' => $countryCode]);
            } else {
                $result = $this->getDoctrine()->getRepository(HolidayQuery::class)->findOneBy([
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'country_code' => $countryCode,
                    'region' => $region]);
            }

            $result = $result->getResult();
        }
        
        // get current date
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        $isPublicHolidayURL = 'https://kayaposoft.com/enrico/json/v2.0?action=isPublicHoliday&date=' . $day . '-' . $month . '-' . $year . '&country=' . $countryCode;
        $response = $this->client->request('GET', $isPublicHolidayURL);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200)
            throw new \Exception('Error: can\'t get is public holiday.');

        $isPublicHoliday = $response->getContent();
        $isPublicHoliday = $response->toArray();
        
        $isWorkDayURL = 'https://kayaposoft.com/enrico/json/v2.0/?action=isWorkDay&date=' . $day . '-' . $month . '-' . $year . '&country=' . $countryCode;
        $response = $this->client->request('GET', $isWorkDayURL);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200)
            throw new \Exception('Error: can\'t get is work date.');

        $isWorkDay = $response->getContent();
        $isWorkDay = $response->toArray();

        // set current day's status
        if ($isPublicHoliday['isPublicHoliday'])
            $todayStatus = 'holiday';
        else if ($isWorkDay['isWorkDay'])
            $todayStatus = 'workday';
        else
            $todayStatus = 'free day';

        // calculate maximum days (free days + holidays) in a row
        $begin = new \DateTime(date("Y-m-d", strtotime($fromDate)));
        $end = new \DateTime(date("Y-m-d", strtotime($toDate)));
        $end->modify('+1 day');

        $holidays = [];
        for ($i = 0; $i < sizeof($result); $i++) {
            $tempMonth = $result[$i]['date']['month'];
            $tempDay = $result[$i]['date']['day'];

            if ($tempMonth < 10)
                $tempMonth = '0' . $result[$i]['date']['month'];

            if ($tempDay < 10)
                $tempDay = '0' . $result[$i]['date']['day'];

            $holidays[] = $result[$i]['date']['year'] . '-' . $tempMonth . '-' . $tempDay;
        }

        $a = []; // array which dontains: 1 - if is holiday or Sunday or Saturay, else 0
        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
            $temp = false;
            for ($j = 0; $j < sizeof($holidays); $j++) {
                if ((strtotime($i->format("Y-m-d")) == strtotime($holidays[$j])) || (date("N", strtotime($i->format("Y-m-d"))) == 6) || (date("N", strtotime($i->format("Y-m-d"))) == 7))
                    $temp = true;
            }
            if ($temp)
                $a[] = 1;
            else
                $a[] = 0;
        }

        $b = []; // for storing max values
        $max = 0;
        for ($i = 0; $i < sizeof($a); $i++) {
            if ($a[$i] == 0)
                $max = 0;
            else {
                $max++;
                $b[] = $max;
            }
        }

        if (sizeof($b) > 0)
            $maxDaysInARow = max($b);
        else
            $maxDaysInARow = 0;

        return $this->render('show.html.twig', [
            'results' => $result,
            'todayStatus' => $todayStatus,
            'maxDaysInARow' => $maxDaysInARow]);
        }
}

function rekeyMultiArray($array) {
	$temp = array();
	$counter = 0;
	if (is_array($array)) {
		foreach ($array as $key => $val) {
			if (!is_numeric($key)) {
				$temp[$key] = rekeyMultiArray($val);
			} else {
				$temp[$counter] = rekeyMultiArray($val);
				$counter++;
			}
		}
		return $temp;
	} else
		return $array;
}