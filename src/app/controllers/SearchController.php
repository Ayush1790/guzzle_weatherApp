<?php

use Phalcon\Mvc\Controller;
use GuzzleHttp\Client;

require_once BASE_PATH . '/vendor/autoload.php';
class SearchController extends Controller
{
    public function indexAction()
    {
        $name = $this->request->getPost('search');
        if (isset($name)) {
            $this->session->set('name', $name);
        }
        $type = $this->request->get('type');
        if (!isset($type)) {
            $type = 'current';
        }
        if (!isset($name)) {
            $name =  $this->session->get('name');
        }
        $name = str_replace(" ", "%20", $name);
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.weatherapi.com',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);
        if ($type == 'history') {
            $response = $client->request('POST', '/v1/' .
                $type . '.json?key=0bab7dd1bacc418689b143833220304&q=' . $name . '&aqi=yes&dt=05-05-23');
        } elseif ($type == 'airquality' || $type == 'alerts') {
            $response = $client->request('POST', '/v1/current.json?key=0bab7dd1bacc418689b143833220304&q=' .
                $name . '&aqi=yes&alerts=yes');
        } else {
            $url = "/v1/$type.json?key=0bab7dd1bacc418689b143833220304&q=$name&alerts=yes";
            $response = $client->request('POST', $url);
        }
        $view = json_decode($response->getBody(), true);
        $url = "/v1/current.json?key=0bab7dd1bacc418689b143833220304&q=$name";
        $response = $client->request('POST', $url);
        $data = json_decode($response->getBody(), true);
        $this->view->data = $data;
        $this->view->type = $type;
        $this->view->view = $view;
    }
}
