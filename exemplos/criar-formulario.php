<?php

use nortedevbr\eventoprobr\forms\FormGenerator\FormOptions;

require "../vendor/autoload.php";

$form = new \nortedevbr\eventoprobr\forms\FormGenerator();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gerador de formulário</title>
</head>
<body>
<?php

$form->setFormId('teste');
$form->setFormName('teste');
$form->setFormAction('teste');

$form->setFormParent('div', null, ["class" => "row g-3"]);
$form->setFormParent('div', null, ["class" => "col-12"]);
$form->setFormBrother('h5', 'Configurações', ["class" => "font-size-14 mb-4"])
    ->child('i', null, ["class" => "mdi mdi-arrow-right text-primary me-1"], true, true);

$form->inputHidden('usuarios_id', 1);
$form->input('titulo', null, 'titulo', ["class" => "form-control", "placeholder" => "Título", "data-parsley-validar" => "evento-titulo", "data-parsley-trigger" => "input", "required"], 'text')
    ->parent('div', null, ["class" => "mb-3"])
    ->label('Título', ["class" => "form-label", "for" => "titulo"])
    ->helper('span', 'URL do evento: <strong id="urlDoEvento">seu-evento</strong>', ["class" => "text-muted"]);

$form->input('nome', null, 'titulo', ["class" => "form-control", "placeholder" => "Título", "data-parsley-validar" => "evento-titulo", "data-parsley-trigger" => "input", "required"], 'text')
    ->label('Nome', ["class" => "form-label", "for" => "nome"])
    ->helper('small', 'Sem o parent')
    ->parent('div')
    ->dataList(FormOptions::byArray(null, null, ['novo', 'editar', 'deletar']));

$form->checkbox('relembrar', null, 'relembrar', ["class" => "form-control", "required"])
    ->label('relembrar', ["class" => "form-label", "for" => "relembrar"])
    ->parent('div');

$form->checkboxOptions(
    'options',
    null,
    'options',
    FormOptions::byArray(null, null, ['novo', 'editar', 'deletar']),
    ["class" => "form-control", "required"],
    ["class" => "d-flex"]
)
    ->label('Opções', ["class" => "form-label", "for" => "options"])
    ->parent('div');

$form->radio('aceitar-termos', null, 'aceitar-termos', ["class" => "form-control", "required"])
    ->label('Aceitar termos', ["class" => "form-label", "for" => "aceitar-termos"])
    ->parent('div');

echo $form->show();
?>
</body>
</html>
