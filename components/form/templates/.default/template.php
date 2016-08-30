<?php

/* @var $form Jugger\Form\Form */
/* @var $labels array */
/* @var $result string */

extract($arResult);

$attributes = $form->getAttributes();
$labels = isset($arParams['labels']) ? $arParams['labels'] : [];
$errors = $form->getErrors();

if (!empty($labels)) {
    $tmp = [];
    foreach (array_keys($labels) as $key) {
        $tmp[$key] = $attributes[$key];
    }
    $attributes = $tmp;
    unset($tmp);
}

?>
<?php
if ($success) {
    echo "<div class='alert alert-success'>{$success}</div>";
}
elseif ($errors) {
    $errorsMessage = "";
    foreach ($errors as $name => $value) {
        if ($value === 'Jugger\Validator\RequireValidator') {
            $errorsMessage[] = "Поле '{$name}' должно быть заполнено";
        }
        else {
            $errorsMessage[] = $value;
        }
    }
    echo "<div class='alert alert-danger'>". implode("<br>", $errorsMessage) ."</div>";
}
?>
<?php
if ($form->id) {
    $id = " id='{$form->id}' ";
}
echo "<form {$id} method='POST'>";
foreach ($attributes as $name => $attribute):
    $label = isset($labels[$name]) ? $labels[$name] : $name;
    $nameHtml = $name;
    if ($form->id) {
        $nameHtml = $form->id."[{$name}]";
    }
    //
    $field = "<input type='text' name='{$nameHtml}' value='{$value}' class='form-control'>";
?>
    <div class="form-group">
        <label>
            <?= $label ?>
        </label>
        <?= $field ?>
    </div>
<?php
endforeach;
?>
<div class="form-group">
    <button type="submit" class="btn btn-submit">
        Отправить
    </button>
</div>
</form>