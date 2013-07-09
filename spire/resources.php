<?php

namespace Spire\Resources;

use MongoException;

class Resource {

    /*
     * variable that holds all the $_REQUEST data.
     * @var array
     * @access public
     */

    public $requestData;

    /*
     * mongodb collection variable for database level operations.
     * @access public
     * @class MongoCollection
     */

    public $collection;

    /*
     * hook before the response.
     * could be usefull for authentication.
     */

    public function beforeRequest() {

    }

    /*
     * hook after the response.
     */

    public function afterRequest() {

    }

    public function __construct($collection, $requestData) {
        $this->beforeRequest();
        $this->afterRequest();

        $this->requestData = $requestData;
        $this->collection = $collection;
    }

    public function listItems() {

        $limit = $this->requestData->get('limit', 20);
        $offset = $this->requestData->get('offset', 0);

        $cursor = $this->collection->find()->skip($offset)->limit($limit);


        $bundleMethodName = sprintf('build_%s_bundle', $this->collection->getName());
        $results = array();

        foreach ($cursor as $doc) {
            if(method_exists($this, $bundleMethodName)) {
                $doc = $this->$bundleMethodName($doc);
            }

            $results[] = $doc;

        }

        $responseData = array(
            'meta' => array(
                'limit' => $limit,
                'offset' => $offset,
                'total_count' => $cursor->count(),
            ),
            'objects' => $results
        );

        return $responseData;
    }

    public function getItem($uniqueID) {
        $item = $this->collection->findOne(array('_id' => $uniqueID));

        $bundleMethodName = sprintf('build_%s_bundle', $this->collection->getName());
        if(method_exists($this, $bundleMethodName)) {
            $item = $this->$bundleMethodName($item);
        }

        return $item;
    }

    public function createItem($item) {
        $this->collection->insert($item, array("w"));
        return $item;
    }

    /*
     * @todo: this method is not supported since silex does not support PATCH as a request method.
     */
    public function updateItem($uniqueID, $item) {
        $this->collection->update(array("_id"=> $uniqueID), array('$set' => $item));
        return $item;
    }

    public function replaceItem($uniqueID, $item) {
        $this->collection->update(array("_id"=> $uniqueID), $item);
        return $item;
    }

    public function deleteItem($uniqueID) {
        $this->collection->remove(array("_id" => $uniqueID));
        return $uniqueID;
    }


}