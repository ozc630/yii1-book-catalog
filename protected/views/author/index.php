<h2>Authors</h2>

<?php if (!Yii::app()->user->isGuest): ?>
    <p><?php echo CHtml::link('Create Author', ['create'], ['class' => 'btn']); ?></p>
<?php endif; ?>

<?php if (empty($authors)): ?>
    <p>No authors found.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Books count</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($authors as $author): ?>
            <tr>
                <td><?php echo (int) $author->id; ?></td>
                <td><?php echo CHtml::encode($author->name); ?></td>
                <td><?php echo (int) $author->books_count; ?></td>
                <td class="actions">
                    <?php echo CHtml::link('View', ['view', 'id' => $author->id], ['class' => 'btn btn-secondary']); ?>

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
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (isset($pages) && $pages->pageCount > 1): ?>
        <?php $this->widget('CLinkPager', ['pages' => $pages]); ?>
    <?php endif; ?>
<?php endif; ?>
