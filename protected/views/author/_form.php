<?php $form = $this->beginWidget('CActiveForm', ['id' => 'author-form']); ?>

<?php echo $form->errorSummary($model); ?>

<div class="field">
    <?php echo $form->labelEx($model, 'name'); ?>
    <?php echo $form->textField($model, 'name', ['maxlength' => 255, 'style' => 'width: 100%;']); ?>
    <?php echo $form->error($model, 'name'); ?>
</div>

<div class="field">
    <?php echo $form->labelEx($model, 'bio'); ?>
    <?php echo $form->textArea($model, 'bio', ['rows' => 6, 'style' => 'width: 100%;']); ?>
    <?php echo $form->error($model, 'bio'); ?>
</div>

<div class="field">
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn']); ?>
    <?php echo CHtml::link('Cancel', ['index'], ['class' => 'btn btn-secondary']); ?>
</div>

<?php $this->endWidget(); ?>
