# Code validation service for Yii2 models

Validate model change via code delivered by external service (SMS, email & etc)

## Installation

Via Composer
```
composer require vsevolod-ryzhov/yii2-code-validation
```

## Usage

Inject service in your Controller:

```php
private $service;

public function __construct($id, $module, \vsevolodryzhov\yii2CodeValidation\Service $service, $config = [])
{
    parent::__construct($id, $module, $config);
    $this->service = $service;
}
```

create action with some form (Model or ActiveRecord):

```php
public function actionChangeRequest()
{
    $form = new Form();

    if ($form->load(Yii::$app->request->post()) && $form->validate()) {
        // if form submitted and validated use "set" function to store form and get generated code
        $code = $this->service->set($form);
        // send $code here and then redirect to verify action
        return $this->redirect('/verify');
    }

    return $this->render('change', ['form' => $form]);
}
```

create verify action:

```php
public function actionVerify()
{
    if (!$this->service->exists()) {
        // if nothing to verify - throw new Exception, redirect or do something else
    }

    // create CodeValidationForm (included in this package) with stored code and new instance of same form, created in previews action
    $form = new \vsevolodryzhov\yii2CodeValidation\CodeValidationForm($this->service->getCode(), new Form($this->service->getData()));

    if ($form->load(Yii::$app->request->post()) && $form->validate()) {
        // clear all data on success
        $this->service->clear();
        // refresh or redirect to success page
        return $this->refresh();
    }

    return $this->render('verify', ['form' => $form]);
}
```

also, you can create additional action for updating existing code

```php
public function actionUpdate()
{
    $response = $this->service->renewCode();
}
```

```renewCode``` action returns ```\vsevolodryzhov\yii2CodeValidation\Response``` object with some useful information:
- ```$response->getDone()``` returns true if code was updated
- ```$response->getWait()``` returns number of seconds time until the next possible update time
- ```$response->getCode()``` returns new code (if generated) or null