<h2>Author #<?php echo (int) $author->id; ?></h2>

<p><strong>Name:</strong> <?php echo CHtml::encode($author->name); ?></p>

<?php if (!empty($author->bio)): ?>
    <p><strong>Bio:</strong><br><?php echo nl2br(CHtml::encode($author->bio)); ?></p>
<?php endif; ?>

<h3>Books</h3>

<?php if (empty($author->books)): ?>
    <p>No books linked to this author.</p>
<?php else: ?>
    <ul>
        <?php foreach ($author->books as $book): ?>
            <li>
                <?php echo CHtml::link(CHtml::encode($book->title), ['/book/view', 'id' => $book->id]); ?>
                (<?php echo (int) $book->published_year; ?>)
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p>
    <?php echo CHtml::link('Subscribe for SMS notifications', ['/subscription/create', 'author_id' => $author->id], ['class' => 'btn']); ?>
</p>

<div class="actions">
    <?php echo CHtml::link('Back to list', ['index'], ['class' => 'btn btn-secondary']); ?>

    <?php if (!Yii::app()->user->isGuest): ?>
        <?php echo CHtml::link('Edit', ['update', 'id' => $author->id], ['class' => 'btn']); ?>

        <?php echo CHtml::beginForm(['delete', 'id' => $author->id], 'post', ['class' => 'inline']); ?>
            <?php echo CHtml::hiddenField(Yii::app()->request->csrfTokenName, Yii::app()->request->csrfToken); ?>
            <?php echo CHtml::submitButton('Delete', [
                'class' => 'btn btn-danger',
                'onclick' => "return confirm('Delete this author?');",
            ]); ?>
        <?php echo CHtml::endForm(); ?>
    <?php endif; ?>
</div>
