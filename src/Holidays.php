<?php

namespace Sandbox\Google;

class Holidays
{

	private $apiKey;
	private $countryCode;
	private $startDate;
	private $endDate;
	private $minimal = false;
	private $datesOnly = false;

	public function __construct()
	{
		$this->startDate = date('Y-m-d') . 'T00:00:00-00:00';
		$this->endDate = (date('Y') + 1) . '-01-01T00:00:00-00:00';
	}

	public function inYear($year)
	{
		$this->startDate = date('Y-m-d', strtotime($year . '-01-01')) . 'T00:00:00-00:00';
		$this->endDate = date('Y-m-d', strtotime($year . '-12-31')) . 'T00:00:00-00:00';

		return $this;
	}

	public function from($string)
	{
		$this->startDate = date('Y-m-d', strtotime($string)) . 'T00:00:00-00:00';

		return $this;
	}

	public function to($string)
	{
		$this->endDate = date('Y-m-d', strtotime($string)) . 'T00:00:00-00:00';

		return $this;
	}

	public function withApiKey($apiKey)
	{
		$this->apiKey = $apiKey;
		return $this;
	}

	public function inCountry($countryCode)
	{
		$this->countryCode = strtolower($countryCode);

		return $this;
	}

	public function withMinimalOutput()
	{
		$this->minimal = true;

		return $this;
	}

	public function withDateOnlyOutput()
	{
		$this->datesOnly = true;

		return $this;
	}

	public function get()
	{
		if (!$this->apiKey) {
			$this->apiKey = env('GOOGLE_API_KEY');
			if (!$this->apiKey) {
				throw new \Exception('You must set GOOGLE_API_KEY in your .env file or pass it to the withApiKey() method.');
			}
		}

		if (!$this->countryCode) {
			throw new \Exception('You must pass a country code to the inCountry() method.');
		}

		$result = [];

		$apiUrl = "https://content.googleapis.com/calendar/v3/calendars/en.{$this->countryCode}%23holiday%40group.v.calendar.google.com/events" .
			'?singleEvents=false' .
			"&timeMax={$this->endDate}" .
			"&timeMin={$this->startDate}" .
			"&key={$this->apiKey}";

		$response = json_decode(file_get_contents($apiUrl), true);

		if (isset($response['items'])) {
			if ($this->datesOnly === true) {
				foreach ($response['items'] as $holiday) {
					$result[] = $holiday['start']['date'];
				}

				sort($result);
			} elseif ($this->minimal === true) {
				foreach ($response['items'] as $holiday) {
					$result[] = [
						'name' => $holiday['summary'],
						'date' => $holiday['start']['date'],
					];
				}

				usort($result, function ($a, $b) {
					if ($a['date'] == $b['date']) {
						return 0;
					}

					return ($a['date'] < $b['date']) ? -1 : 1;
				});
			} else {
				$result = $response['items'];

				usort($result, function ($a, $b) {
					if ($a['start']['date'] == $b['start']['date']) {
						return 0;
					}

					return ($a['start']['date'] < $b['start']['date']) ? -1 : 1;
				});
			}
		}

		return $result;
	}
}
