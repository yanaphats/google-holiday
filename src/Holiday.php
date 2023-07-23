<?php

namespace Sandbox\Google;

class Holiday
{

	private $key;
	private $locale = 'en';
	private $country;
	private $startDate;
	private $endDate;
	private $simple = false;
	private $datesOnly = false;

	public function __construct()
	{
		$this->startDate = date('Y') . '-01-01T00:00:00-00:00';
		$this->endDate = date('Y') . '-12-31T00:00:00-00:00';
	}

	public function year($year)
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

	public function key($key)
	{
		$this->key = $key;
		return $this;
	}

	public function locale($locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function country($code)
	{
		$this->country = strtolower($code);

		return $this;
	}

	public function simple()
	{
		$this->simple = true;

		return $this;
	}

	public function dateOnly()
	{
		$this->datesOnly = true;

		return $this;
	}

	public function get()
	{
		if (!$this->key) {
			$this->key = env('GOOGLE_API_KEY');
			if (!$this->key) {
				throw new \Exception('You must set GOOGLE_API_KEY in your .env file or pass it to the key() method.');
			}
		}

		if (!$this->country) {
			throw new \Exception('You must pass a country code to the country() method.');
		}

		$result = [];

		$apiUrl = "https://content.googleapis.com/calendar/v3/calendars/{$this->locale}.{$this->country}%23holiday%40group.v.calendar.google.com/events" .
			'?singleEvents=false' .
			"&timeMax={$this->endDate}" .
			"&timeMin={$this->startDate}" .
			"&key={$this->key}";

		$response = json_decode(file_get_contents($apiUrl), true);

		if (isset($response['items'])) {
			if ($this->datesOnly === true) {
				foreach ($response['items'] as $holiday) {
					$result[] = $holiday['start']['date'];
				}

				sort($result);
			} elseif ($this->simple === true) {
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
