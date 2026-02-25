<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f5f7;
            color: #1f2937;
        }

        header {
            background: #111827;
            color: #fff;
            padding: 16px 24px;
        }

        header h1 {
            margin: 0 0 10px 0;
            font-size: 20px;
        }

        nav a {
            color: #fff;
            margin-right: 14px;
            text-decoration: none;
        }

        nav a:hover {
            text-decoration: underline;
        }

        nav .nav-inline-form {
            display: inline;
            margin: 0;
        }

        nav .nav-link-button {
            color: #fff;
            margin-right: 14px;
            text-decoration: none;
            background: none;
            border: 0;
            padding: 0;
            cursor: pointer;
            font: inherit;
        }

        nav .nav-link-button:hover {
            text-decoration: underline;
        }

        main {
            max-width: 1200px;
            margin: 24px auto;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .06);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
        }

        .actions-col {
            width: 1%;
            white-space: nowrap;
        }

        .actions {
            white-space: nowrap;
        }

        .action-buttons {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .actions .inline {
            margin: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 8px 14px;
            background: #2563eb;
            color: #fff;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            line-height: 1;
            box-sizing: border-box;
            transition: background-color .15s ease-in-out;
        }

        .btn:hover {
            background: #1d4ed8;
            text-decoration: none;
        }

        .btn-danger {
            background: #dc2626;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-secondary {
            background: #6b7280;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .field {
            margin-bottom: 12px;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .flash-success,
        .flash-error,
        .flash-info {
            padding: 10px 12px;
            border-radius: 4px;
            margin-bottom: 12px;
        }

        .flash-success {
            background: #dcfce7;
            color: #166534;
        }

        .flash-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .flash-info {
            background: #e0f2fe;
            color: #0c4a6e;
        }

        .errorMessage {
            color: #b91c1c;
            font-size: 13px;
        }

        .errorSummary {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 12px;
        }

        img.cover {
            max-width: 180px;
            border-radius: 4px;
        }

        .inline {
            display: inline;
        }

        @media (max-width: 768px) {
            main {
                margin: 12px;
                padding: 14px;
            }

            .actions {
                white-space: normal;
            }

            .action-buttons {
                flex-wrap: wrap;
            }

            .btn {
                min-height: 32px;
                padding: 7px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1><?php echo CHtml::encode(Yii::app()->name); ?></h1>
        <nav>
            <?php echo CHtml::link('Books', ['/book/index']); ?>
            <?php echo CHtml::link('Authors', ['/author/index']); ?>
            <?php echo CHtml::link('Top Authors Report', ['/report/topAuthors']); ?>

            <?php if (Yii::app()->user->isGuest): ?>
                <?php echo CHtml::link('Login', ['/site/login']); ?>
            <?php else: ?>
                <?php echo CHtml::beginForm(['/site/logout'], 'post', ['class' => 'nav-inline-form']); ?>
                <?php echo CHtml::hiddenField(Yii::app()->request->csrfTokenName, Yii::app()->request->csrfToken); ?>
                <?php echo CHtml::submitButton(
                    'Logout (' . CHtml::encode(Yii::app()->user->name) . ')',
                    ['class' => 'nav-link-button']
                ); ?>
                <?php echo CHtml::endForm(); ?>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php foreach (['success', 'error', 'info'] as $flashType): ?>
            <?php if (Yii::app()->user->hasFlash($flashType)): ?>
                <div class="flash-<?php echo $flashType; ?>">
                    <?php echo CHtml::encode(Yii::app()->user->getFlash($flashType)); ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php echo $content; ?>
    </main>
</body>

</html>