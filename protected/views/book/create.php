<h2>Create Book</h2>

<?php echo $this->renderPartial('_form', [
    'model' => $model,
    'authors' => $authors,
    'selectedAuthorIds' => $selectedAuthorIds,
]); ?>
