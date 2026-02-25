<h2>Login</h2>
<p>Use seeded credentials from README.</p>

<?php $form = $this->beginWidget('CActiveForm', ['id' => 'login-form']); ?>

<?php echo $form->errorSummary($model); ?>

<div class="field">
    <?php echo $form->labelEx($model, 'username'); ?>
    <?php echo $form->textField($model, 'username', ['maxlength' => 100]); ?>
    <?php echo $form->error($model, 'username'); ?>
</div>

<div class="field">
    <?php echo $form->labelEx($model, 'password'); ?>
    <?php echo $form->passwordField($model, 'password'); ?>
    <?php echo $form->error($model, 'password'); ?>
</div>

<div class="field">
    <label>
        <?php echo $form->checkBox($model, 'rememberMe'); ?>
        <?php echo CHtml::encode($model->getAttributeLabel('rememberMe')); ?>
    </label>
</div>

<div class="field">
    <?php echo CHtml::submitButton('Login', ['class' => 'btn']); ?>
</div>

<?php $this->endWidget(); ?>
