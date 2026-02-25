<h2>Books</h2>

<?php if (!Yii::app()->user->isGuest): ?>
    <p><?php echo CHtml::link('Create Book', ['create'], ['class' => 'btn']); ?></p>
<?php endif; ?>

<?php if (empty($books)): ?>
    <p>No books found.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>ISBN</th>
                <th>Published Year</th>
                <th>Authors</th>
                <th class="actions-col">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?php echo (int) $book->id; ?></td>
                <td><?php echo CHtml::encode($book->title); ?></td>
                <td><?php echo CHtml::encode($book->isbn); ?></td>
                <td><?php echo (int) $book->published_year; ?></td>
                <td><?php echo CHtml::encode($book->getAuthorNames()); ?></td>
                <td class="actions">
                    <div class="action-buttons">
                        <?php echo CHtml::link('View', ['view', 'id' => $book->id], ['class' => 'btn btn-secondary']); ?>

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
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
