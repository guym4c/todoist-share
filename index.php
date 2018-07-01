<?php

$ignoreNames = ['errands', 'shopping'];
$projects = getTodoistData('projects');

$ignoreIDs = [];
foreach ($projects as $project) {
	if (in_array(strtolower($project['name']), $ignoreNames)) {
		$ignoreIDs[] = $project['id'];
	}
}

print_r($ignoreIDs);

$tasks = getTodoistData('tasks');
foreach ($tasks as $i => $task) {
	if ($task['completed'] || in_array($task['project_id'], $ignoreIDs)) {
		unset($tasks[$i]);
	}
}
$tasks = array_values($tasks);

function getTodoistData($uri) {

	include __DIR__ . '/keys.php';
	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL 			=> "https://beta.todoist.com/API/v8/$uri",
  		CURLOPT_RETURNTRANSFER 	=> true,
  		CURLOPT_HTTPHEADER 		=> ["Authorization: Bearer $todoistApiKey"],
  	]);

	$result = curl_exec($curl);
	$error = curl_error($curl);
	curl_close($curl);
	if ($error) {
		return $error;
	} else {
		return json_decode($result, true); 
	}

}


?>
