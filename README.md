# Installation
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```
composer require sovngare/yii2-array-validator
```

# Usage
Use class: `sovngare\validators\ArrayValidator`
## Example №1
Validate true if property "fields" will be array.
```php
public function rules()
{
    return [
        ['fields', 'required'],
        ['fields', ArrayValidator::class]
    ];
}
```

## Example №2
Validate true if status[code] key will be integer in the range 100 and 500.
```php
public function rules()
{
    return [
        ['fields', ArrayValidator::class, 'key' => 'status.code', 'rules' => [
            ['integer', 'min' => 100, 'max' => 500]
        ]]
    ];
}
```

## Example №3
By default key label is "Attribute". Model errors log:
```json
{
    "fields": [
        "Attribute must be no less than 100."
    ]
}
```
To change label you must send param label:
```php
public function rules()
{
    return [
        ['fields', ArrayValidator::class, 'key' => 'status.code', 'rules' => [
            ['integer', 'min' => 100, 'max' => 500]
        ], 'label' => 'status_code']
    ];
}
```
Model errors log now:
```json
{
    "fields": [
        "Status Code must be no less than 100."
    ]
}
```

## Validate images
Data from client:
```
phone:7087952412
fields[name]:Sovngare
fields[avatar]:(\yii\web\UploadedFile)
```
Model:
```php
class TestModel extends Model
{
    public $phone;
    public $fields;

    public function rules()
    {
        return [
            [['phone', 'fields'], 'required'],
            ['fields', ArrayValidator::class, 'key' => 'name', 'rules' => [
                ['required'],
                ['string', 'length' => [4, 16]]
            ], 'label' => 'name'],
            ['fields', ArrayValidator::class, 'key' => 'avatar', 'rules' => [
                ['required'],
                ['image', 'maxSize' => 1024 * 1024 * 3]
            ], 'label' => 'avatar'],
        ];
    }
}
```
Controller
```php
public function actionIndex()
{
    $model = new TestModel();
    $model->attributes = Yii::$app->request->getBodyParams();

    if (is_array($model->fields)) {
        $model->fields['avatar'] = UploadedFile::getInstanceByName('fields[avatar]');
    }

    if (!$model->validate()) {
        return $model->errors;
    }

    return $model->attributes;
}
```
