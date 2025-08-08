<?php

namespace App\Console\Commands;

use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\Collections\CompaniesCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\InvalidArgumentException;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\SelectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\UrlCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\SelectCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\UrlCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\SelectCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\UrlCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use App\Models\Company;
use Illuminate\Console\Command;

class PushEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:push-emails {limit?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws InvalidArgumentException
     * @throws AmoCRMMissedTokenException
     */
    public function handle()
    {
        $apiClient = new \AmoCRM\Client\AmoCRMApiClient();

        $longLivedAccessToken = new LongLivedAccessToken(env('KOMMO_LONG'));

        $apiClient->setAccessToken($longLivedAccessToken)
            ->setAccountBaseDomain('liberpay.amocrm.com');

        $companies = Company::query()
            ->where('status', 1)
            ->limit($this->argument('limit'))
            ->get();

        foreach ($companies as $companyModel) {
            try {

                $name = str_replace('https://', '', $companyModel->url);

                $company = new CompanyModel();
                $company->setName($name);
                $company->setResponsibleUserId(11986759); //stefan

                $companiesCollection = new CompaniesCollection();
                $companiesCollection->add($company);

                $companyCRM = $apiClient->companies()->add($companiesCollection)->first();
            } catch (AmoCRMApiException|AmoCRMMissedTokenException $e) {
                dd($e->getMessage());
            }

            $customFields = new CustomFieldsValuesCollection();

            if (empty($companyModel->emails)) {

                $companyModel->status = 8;
                $companyModel->save();

                continue;
            }

            foreach (json_decode($companyModel->emails) as $email) {

                $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
                $emailField->setValues(
                    (new MultitextCustomFieldValueCollection())
                        ->add(
                            (new MultitextCustomFieldValueModel())
                                ->setEnum('WORK')
                                ->setValue($email)
                        )
                );

                $customFields->add($emailField);
            }

            $urlValue = new UrlCustomFieldValuesModel();
            $urlValue->setFieldId(534602); //web
            $urlValue->setValues(
                (new UrlCustomFieldValueCollection())
                    ->add(
                        (new UrlCustomFieldValueModel())
                            ->setValue($companyModel->url)
                    )
            );

            $customFields->add($urlValue);

            $companyCRM->setCustomFieldsValues($customFields);

            try {
                $apiClient->companies()->updateOne($companyCRM);

            } catch (AmoCRMApiException $e) {
                dd($e->getMessage());
            }


            $leadsService = $apiClient->leads();

            $lead = new LeadModel();
            $lead->setName($name)
                ->setStatusId(88196676) //url
                ->setResponsibleUserId(11986759) //stefan
                ->setCompany(
                    (new CompanyModel())
                        ->setId($companyCRM->getId())
                );

            $leadsCollection = new LeadsCollection();
            $leadsCollection->add($lead);

            try {
                $lead = $leadsService->add($leadsCollection)->first();

                $companyModel->status = 2;
                $companyModel->lead_id = $lead->getId();
                $companyModel->company_id = $company->getId();
                $companyModel->save();

            } catch (AmoCRMApiException $e) {

                dd($e->getMessage());
            }
        }
    }
}
