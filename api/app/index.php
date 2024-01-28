<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Controllers\Auth;
use \AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use Symfony\Component\Dotenv\Dotenv;

$url = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$answ = ['status' => '', 'message' => ''];

$dotenv = new Dotenv();
$dotenv->load(__DIR__ .'/.env');   

$clientId = $_ENV['CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];
$redirectUri = $_ENV['CLIENT_REDIRECT_URI'];
$site_url = $_ENV['URL'];

switch ($url) {
    case preg_match('/\/amocrm\/api\/app\/auth?.+/', $url) ? true : false:
        if ($method == 'GET') {
            $auth = new Auth($clientId, $clientSecret, $redirectUri);
            $token = $auth->getAuthToken();

            if (!$token) {
                $answ['status'] = 'error';
                $answ['message'] = 'Authentication error';
            } else {
                $answ['status'] = 'success';
                $answ['message'] = 'Authentication success';
            }

            header('Location: ' . $site_url);
        }
        break;
    case preg_match('/\/amocrm\/api\/app\/lead\/add?.+/', $url) ? true : false:

        if ($method == 'POST') {

            $auth = new Auth($clientId, $clientSecret, $redirectUri);
            $token = $auth->getAuthToken();

            if (!$token) {
                $answ['status'] = 'error';
                $answ['message'] = 'Need to be logged in';
                header("Content-Type: application/json");
                echo json_encode($answ);
                exit;
            } else {

                try {
                    $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
                    $apiClient->setAccessToken($token)->setAccountBaseDomain($token->getValues()['baseDomain']);

                    $leadsService = $apiClient->leads();

                    $price = $_POST["price"];
                    $name = $_POST["name"];
                    $email = $_POST["email"];
                    $phone = $_POST["tel"];

                    //Создадим сделку с заполненным бюджетом и привязанными контактом
                    $lead = new LeadModel();
                    $lead->setName('Тестовая сделка')
                        ->setPrice($price)
                        ->setContacts(
                            (new ContactsCollection())
                                ->add(
                                    (new ContactModel())
                                        ->setFirstName($name)
                                        ->setCustomFieldsValues(
                                            (new CustomFieldsValuesCollection())
                                                ->add(
                                                    (
                                                        new MultitextCustomFieldValuesModel()
                                                    )->setFieldCode("PHONE")
                                                        ->setValues(
                                                            (new MultitextCustomFieldValueCollection())
                                                                ->add((new MultitextCustomFieldValueModel())->setValue($phone))
                                                        )
                                                )
                                                ->add(
                                                    (
                                                        new MultitextCustomFieldValuesModel()
                                                    )->setFieldCode('EMAIL')
                                                        ->setValues(
                                                            (new MultitextCustomFieldValueCollection())
                                                                ->add((new MultitextCustomFieldValueModel())->setValue($email))
                                                        )
                                                )
                                        )
                                )
                        );

                    $leadsCollection = new LeadsCollection();
                    $leadsCollection->add($lead);
                    $leadsCollection = $leadsService->addComplex($leadsCollection);

                    $answ['status'] = 'success';
                    $answ['message'] = 'Leads are created';
                } catch (Exception $e) {
                    $answ['status'] = 'error';
                    $answ['message'] = $e->getMessage();
                }
            }
        } else {
            $answ['status'] = 'error';
            $answ['message'] = 'Not found';
        }
        break;
    default:
        $answ['status'] = 'error';
        $answ['message'] = 'Not found';
        break;
}

header("Content-Type: application/json");
echo json_encode($answ);
exit;