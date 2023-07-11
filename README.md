# Evento PRO BR Forms Generator
Componente de geração de formuláros

### Instalação
```composer log
composer require nortedevbr/eventoprobr.forms
```

### Inicialização
```php
use nortedevbr\eventoprobr\forms\FormGenerator;

require __DIR__."/vendor/autoload.php";

$form = new FormGenerator();
```

### Configurar atributos dos formulário
```php
$form = new FormGenerator(
    'form_id',
    'form_name',
    'form_action', // URL de envio do formulário
    'post', // Método de envio
    'multipart/form-data'// Tipo de codificação de dados a ser usada no envio para o servidor.
);
```
atributos podem ser informados posteriormente
```php
$form->setFormId();
$form->setFormName();
$form->setFormAction();
$form->setFormMethod();
$form->setFormEnctype();
```
ou podem ser adicionados atributos extras usando `chave,valor`
```php
$form->setFormAttributes('nome_atributo_1','valor_atributo_1');
$form->setFormAttributes('nome_atributo_2','valor_atributo_2');
```
## Modo de usar
[Vide pasta de exemplos](exemplos/)


