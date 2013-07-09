<?php

use Guzzle\Http\Client;


class SpireTest extends PHPUnit_Framework_TestCase {

    public $API_ENDPOINT = 'http://localhost:8090/';

    public function setUp() {

        # create a test environment
        $mongodbConnection = new MongoClient();
        $collection = new MongoCollection($mongodbConnection->selectDB('test'), 'users');
        $collection->remove();

        # add 2 users
        $this->jessica = array(
            "first_name" => "Jessica",
            "last_name" => "Babyvamp",
            "location" => "True Blood"
        );
        $collection->insert($this->jessica);

        $this->kara = array(
            "first_name" => "Kara",
            "last_name" => "Starbucks",
            "location" => "Battlestar Galactica"
        );
        $collection->insert($this->kara);

        $this->collection = $collection;

        $this->client = new Client($this->API_ENDPOINT);
    }

    public function test_01_list() {
        $request = $this->client->get('users/');
        $response = $request->send();
        $decodedResponse = $response->json();

        $this->assertEquals($decodedResponse["meta"]["total_count"], 2);
        $this->assertEquals($decodedResponse["meta"]["limit"], 20);
        $this->assertEquals($decodedResponse["meta"]["offset"], 0);

        $this->assertEquals(sizeof($decodedResponse["objects"]), 2);


        $this->assertEquals($decodedResponse["objects"][0]["first_name"], $this->jessica["first_name"]);
        $this->assertEquals($decodedResponse["objects"][0]["last_name"], $this->jessica["last_name"]);
        $this->assertEquals($decodedResponse["objects"][0]["location"], $this->jessica["location"]);

        $this->assertEquals($decodedResponse["objects"][1]["first_name"], $this->kara["first_name"]);
        $this->assertEquals($decodedResponse["objects"][1]["last_name"], $this->kara["last_name"]);
        $this->assertEquals($decodedResponse["objects"][1]["location"], $this->kara["location"]);


        $this->assertEquals($decodedResponse["objects"][1]["location"], $this->kara["location"]);

    }

    public function test_02_get() {

        $kara = $this->collection->findOne(array("first_name"=>"Kara"));

        $request = $this->client->get(sprintf('users/%s', $kara["_id"]));
        $response = $request->send();
        $decodedResponse = $response->json();

        $this->assertEquals($decodedResponse["first_name"], $this->kara["first_name"]);
        $this->assertEquals($decodedResponse["last_name"], $this->kara["last_name"]);
        $this->assertEquals($decodedResponse["location"], $this->kara["location"]);

    }

    public function test_03_post() {

        $response = $this->client->post('users/', [
            'Content-Type' => 'application/json'
        ], '{"first_name": "Emre", "last_name": "Yılmaz", "location": "Istanbul, Turkey"}')->send();

        $this->assertEquals($response->getStatusCode(), '201');

        $headers = $response->getHeaders();
        $this->assertEquals(isset($headers["Location"]), True);

    }

    public function test_04_put() {
        $kara = $this->collection->findOne(array("first_name"=>"Kara"));

        $response = $this->client->put(sprintf('users/%s/', $kara["_id"]), [
            'Content-Type' => 'application/json'
        ], '{"first_name": "Kara", "last_name": "Starbucks", "location": "Miami, FL"}')->send();

        $this->assertEquals($response->getStatusCode(), '202');

    }

    public function test_05_delete() {
        $kara = $this->collection->findOne(array("first_name"=>"Kara"));

        $response = $this->client->delete(sprintf('users/%s/', $kara["_id"]), [
            'Content-Type' => 'application/json'
        ], '')->send();

        $this->assertEquals($response->getStatusCode(), '204');

    }




}