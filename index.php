<?php
/**
 * Класс для работы с API
 *
 * @author		User Name
 * @version		v.1.0 (dd/mm/yyyy)
 */
class Api
{
	private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
	
	/**
	 * Заполняет строковый шаблон template данными из объекта object
	 *
	 * @author		User Name
	 * @version		v.1.0 (dd/mm/yyyy)
	 * @param		string $template
	 * @return		string
	 */
	public function get_api_path(string $template): string
    {
        return preg_replace_callback('/%(\w+)%/', function ($matches) {
            return $this->inject_data($matches[1]);
        }, $template);
    }

	/**
	 * Метод внедрения конкретного значения в строку (url)
	 *
	 * @author		User Name
	 * @version		v.1.0 (dd/mm/yyyy)
	 * @param		string $key
	 * @return		string
	 */
    private function inject_data(string $key): string
    {
        if (isset($this->data[$key])) {
            return rawurlencode((string) $this->data[$key]);
        }

        return '%' . $key . '%';
    }
}

// Лучше использовать PHPUnit
class ApiTest 
{
	private Api $api;

	public function __construct(Api $api)
	{
		$this->api = $api;
	}

	/**
	 * Тестирует метод get_api_path класса Api.
	 *
	 * Проверяет заполнение шаблонов API данными из объекта Api.
	 * Возвращает true, если все тесты прошли успешно, иначе false.
	 *
	 * @return bool True если все тесты прошли, иначе false.
	 */
	public function test_get_api_path() : bool
	{
		$templates = [
			"/api/items/%id%/%name%",
			"/api/items/%id%/%role%",
			"/api/items/%id%/%salary%"
		];
	
		$expected_results = [
			'/api/items/20/John%20Dow',
			'/api/items/20/Q%20%26%20A',
			'/api/items/20/100'
		];
	
		$all_tests_passed = true;
	
		foreach ($templates as $index => $template) 
		{
			$result = $this->api->get_api_path($template);
			if ($result === $expected_results[$index]) 
			{
				echo "Test passed for template: '$template'\n";
			} 
			else 
			{
				echo "Test failed for template: '$template'\n";
				echo "Expected: '{$expected_results[$index]}', got: '$result'\n";
				$all_tests_passed = false;
			}
		}
	
		return $all_tests_passed;
	}
}

$user =
[
	'id' => 20,
	'name' => 'John Dow',
	'role' => 'Q & A',
	'salary' => 100
];


$api_path_templates =
[
	"/api/items/%id%/%name%",
	"/api/items/%id%/%role%",
	"/api/items/%id%/%salary%"
];


$api = new Api($user);
$apiTest = new ApiTest($api);

$apiTest->test_get_api_path();

$api_paths = array_map(function ($api_path_template) use ($api)
{
	return $api->get_api_path($api_path_template);
}, $api_path_templates);

echo json_encode($api_paths, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);


