<?php

/**
 * Address finder Google file.
 *
 * @see       maps.googleapis.com Documentation  of Google Geocoding API
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace App\Map\Address;

/**
 * Address finder Google class.
 */
class GoogleGeocode extends Base
{
	/**
	 * API Address to retrieve data.
	 *
	 * @var string
	 */
	protected static $url = 'https://maps.googleapis.com/maps/api/geocode/json?';

	/**
	 * {@inheritdoc}
	 */
	public $docUrl = 'https://code.google.com/apis/console/?noredirect';

	/**
	 * {@inheritdoc}
	 */
	public $customFields = [
		'key' => [
			'type' => 'text',
			'validator' => 'required,custom[onlyLetterNumber]'
		],
	];

	/**
	 * {@inheritdoc}
	 */
	public function find($value)
	{
		if (empty($value) || !\App\RequestUtil::isNetConnection()) {
			return [];
		}
		$key = $this->config['key'];
		$lang = \App\Language::getShortLanguageName();
		$response = \Requests::get(static::$url . "key={$key}&address=$value");
		if (!$response->success) {
			\App\Log::warning($response->status_code . ' ' . $response->body, __NAMESPACE__);
			return false;
		}
		$body = \App\Json::decode($response->body);
		$rows = [];
		if (empty($body['error_message']) && isset($body['status'])) {
			if (isset($body['results'][0])) {
				$location = $body['results'][0]['geometry']['location'];
				$urlParam = "key={$key}&language={$lang}&latlng={$location['lat']},{$location['lng']}";
				$response = \Requests::get(static::$url . $urlParam);
				if (!$response->success) {
					\App\Log::warning($response->status_code . ' ' . $response->body, __NAMESPACE__);
					return false;
				}
				$body = \App\Json::decode($response->body);
				if (isset($body['results'])) {
					foreach ($body['results'] as $row) {
						$rows[] = [
							'label' => $row['formatted_address'],
							'address' => $this->parse($row['address_components'])
						];
					}
				}
			}
		} elseif (isset($body['error_message'])) {
			\App\Log::warning("{$body['status']}: {$body['error_message']}", __NAMESPACE__);
			throw new \App\Exceptions\AppException("ERR_COMMUNICATION_ERROR|{$body['status']}: {$body['error_message']}");
		}
		return $rows;
	}

	/**
	 * Parse response.
	 *
	 * @param array $rows
	 *
	 * @return string[]
	 */
	private function parse($rows)
	{
		$address = [];
		foreach ($rows as $row) {
			switch ($row['types'][0]) {
				case 'street_number':
					if (false !== strpos($row['long_name'], '/')) {
						[$address['buildingnumber'], $address['localnumber']] = explode('/', $row['long_name'], 2);
					} else {
						$address['buildingnumber'] = $row['long_name'];
					}
					break;
				case 'route':
					$address['addresslevel8'] = $row['long_name'];
					break;
				case 'postal_code':
					if (empty($row['types'][1])) {
						$address['addresslevel7'] = $row['long_name'];
					}
					break;
				case 'neighborhood':
					$address['addresslevel6'] = $row['long_name'];
					break;
				case 'sublocality':
					$address['addresslevel6'] = $row['long_name'];
					break;
				case 'locality':
					$address['addresslevel5'] = $row['long_name'];
					break;
				case 'administrative_area_level_3':
					$address['addresslevel4'] = $row['long_name'];
					break;
				case 'administrative_area_level_2':
					$address['addresslevel3'] = $row['long_name'];
					break;
				case 'administrative_area_level_1':
					$address['addresslevel2'] = $row['long_name'];
					break;
				case 'country':
					$address['addresslevel1'] = $row['long_name'];
					break;
				default:
					break;
			}
		}
		return $address;
	}
}
