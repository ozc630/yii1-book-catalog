<h2>Book #<?php echo (int) $book->id; ?></h2>

<p><strong>Title:</strong> <?php echo CHtml::encode($book->title); ?></p>
<p><strong>ISBN:</strong> <?php echo CHtml::encode($book->isbn); ?></p>
<p><strong>Published Year:</strong> <?php echo (int) $book->published_year; ?></p>
<p><strong>Authors:</strong> <?php echo CHtml::encode($book->getAuthorNames()); ?></p>

<?php if (!empty($book->cover_url)): ?>
    <p><strong>Cover URL:</strong> <?php echo CHtml::link(CHtml::encode($book->cover_url), $book->cover_url, ['target' => '_blank']); ?></p>
    <p><?php echo CHtml::image($book->cover_url, 'Book cover', ['class' => 'cover']); ?></p>
<?php endif; ?>

<?php if (!empty($book->description)): ?>
    <p><strong>Description:</strong><br><?php echo nl2br(CHtml::encode($book->description)); ?></p>
<?php endif; ?>

<div class="actions">
    <?php echo CHtml::link('Back to list', ['index'], ['class' => 'btn btn-secondary']); ?>

    <?php if (!Yii::app()->user->isGuest): ?>
        <?php echo CHtml::link('Edit', ['update', 'id' => $book->id], ['class' => 'btn']); ?>

        <?php echo CHtml::beginForm(['delete', 'id' => $book->id], 'post', ['class' => 'inline']); ?>
            <?php echo CHtml::hiddenField(Yii::app()->request->csrfTokenName, Yii::app()->request->csrfToken); ?>
            <?php echo CHtml::submitButton('Delete', [
                'class' => 'btn btn-danger',
                'onclick' => "return confirm('Delete this book?');",
            ]); ?>
        <?php echo CHtml::endForm(); ?>
    <?php endif; ?>
</div>
