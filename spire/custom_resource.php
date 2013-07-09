<?php

require_once __DIR__.'/resources.php';


class CustomResource extends \Spire\Resources\Resource {

    public function build_users_bundle($data) {
        $data["fullname"] = sprintf("%s %s", $data["first_name"], $data["last_name"]);
        return $data;
    }


}
