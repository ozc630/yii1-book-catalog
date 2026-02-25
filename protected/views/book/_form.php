<?php
$selectedMap = [];
foreach ((array) $selectedAuthorIds as $authorId) {
    $selectedMap[(int) $authorId] = true;
}
?>

<?php $form = $this->beginWidget('CActiveForm', ['id' => 'book-form']); ?>

<?php echo $form->errorSummary($model); ?>

<div class="field">
    <?php echo $form->labelEx($model, 'title'); ?>
    <?php echo $form->textField($model, 'title', ['maxlength' => 255, 'style' => 'width: 100%;']); ?>
    <?php echo $form->error($model, 'title'); ?>
</div>

<div class="field">
    <?php echo $form->labelEx($model, 'isbn'); ?>
    <?php echo $form->textField($model, 'isbn', ['maxlength' => 20]); ?>
    <?php echo $form->error($model, 'isbn'); ?>
</div>

<div class="field">
    <?php echo $form->labelEx($model, 'published_year'); ?>
    <?php echo $form->textField($model, 'published_year', ['maxlength' => 4]); ?>
    <?php echo $form->error($model, 'published_year'); ?>
</div>

<div class="field">
    <?php echo $form->labelEx($model, 'cover_url'); ?>
    <?php echo $form->textField($model, 'cover_url', ['maxlength' => 1024, 'style' => 'width: 100%;']); ?>
    <?php echo $form->error($model, 'cover_url'); ?>
</div>

<div class="field">
    <?php echo $form->labelEx($model, 'description'); ?>
    <?php echo $form->textArea($model, 'description', ['rows' => 5, 'style' => 'width: 100%;']); ?>
    <?php echo $form->error($model, 'description'); ?>
</div>

<div class="field">
    <label><?php echo CHtml::encode($model->getAttributeLabel('authorIds')); ?></label>

    <?php if (empty($authors)): ?>
        <p>
            No authors found. Create an author first:
            <?php echo CHtml::link('Create Author', ['/author/create']); ?>
        </p>
    <?php else: ?>
        <?php foreach ($authors as $author): ?>
            <label style="display:block; margin-bottom:4px;">
                <?php echo CHtml::checkBox('authorIds[]', isset($selectedMap[(int) $author->id]), [
                    'value' => $author->id,
                    'id' => 'author_' . (int) $author->id,
                ]); ?>
                <?php echo CHtml::encode($author->name); ?>
            </label>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php echo $form->error($model, 'authorIds'); ?>
</div>

<div class="field">
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn']); ?>
    <?php echo CHtml::link('Cancel', ['index'], ['class' => 'btn btn-secondary']); ?>
</div>

<?php $this->endWidget(); ?>
