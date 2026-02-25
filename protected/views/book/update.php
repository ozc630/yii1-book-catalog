<h2>Update Book #<?php echo (int) $model->id; ?></h2>

<?php echo $this->renderPartial('_form', [
    'model' => $model,
    'authors' => $authors,
    'selectedAuthorIds' => $selectedAuthorIds,
]); ?>
