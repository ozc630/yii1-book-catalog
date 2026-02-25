<h2>Subscribe to Author</h2>

<p>
    Author: <strong><?php echo CHtml::encode($author->name); ?></strong>
</p>

<p>
    Enter your phone number in international format (for example, +79991234567).
</p>

<?php $form = $this->beginWidget('CActiveForm', ['id' => 'subscription-form']); ?>

<?php echo $form->errorSummary($model); ?>

<div class="field">
    <?php echo $form->labelEx($model, 'phone'); ?>
    <?php echo $form->textField($model, 'phone', ['maxlength' => 32]); ?>
    <?php echo $form->error($model, 'phone'); ?>
</div>

<div class="field">
    <?php echo CHtml::submitButton('Subscribe', ['class' => 'btn']); ?>
    <?php echo CHtml::link('Back to author', ['/author/view', 'id' => $author->id], ['class' => 'btn btn-secondary']); ?>
</div>

<?php $this->endWidget(); ?>
