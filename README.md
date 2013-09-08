# Balanced Payments integration using cakephp #

## Install Composer ##

If you don't have Composer install it:

    $ curl -s https://getcomposer.org/installer | php

Add this to your composer.json:

    {
    	"require": {
        	"balanced/balanced": "*"
    	}
    }

Refresh your dependencies:

    $ php composer.phar update

Then make sure to require the autoloader and initialize all. I added those codes on the component class file

    require(__DIR__ . '/vendor/autoload.php');
    
    \Httpful\Bootstrap::init();
    \RESTful\Bootstrap::init();
    \Balanced\Bootstrap::init();
    ...

## Controller Codes: ##

    public $components = array('Balanced_Payment');

**Add or Modify customer profile:**


    $this->Balanced_Payment->create_customer($data);

**Add new Bank Account:**
 
    $data['name'] ='account name' 
    $data['routing_number']='bank routing no'
    $data['account_number']='account no'
    $this->Balanced_Payment->create_bank_account($data);
    
**Add bank account to customer:**
 
    $this->Balanced_Payment->add_bank_account_to_customer($customer_id, $bank_account_no);


**Verify Bank account:**
    
    $this->Balanced_Payment->bank_account_verity($bank_account_no);

**Confirm Verify bank account:**
    
     $this->Balanced_Payment->bank_account_verity($bank_account_id);

**Deposit Money :**
    
       $this->Balanced_Payment->debit_on_bank_account($customer_id, $bank_account, $amount, 'message')`;
    