<h2>Top 10 Authors by Book Count</h2>

<?php echo CHtml::beginForm(['/report/topAuthors'], 'get'); ?>
    <div class="field">
        <label for="year">Year (<?php echo (int) $minYear; ?> - <?php echo (int) $maxYear; ?>)</label>
        <?php echo CHtml::textField('year', $year, ['id' => 'year', 'maxlength' => 4]); ?>
        <?php echo CHtml::submitButton('Show', ['class' => 'btn']); ?>
    </div>
<?php echo CHtml::endForm(); ?>

<?php if (empty($rows)): ?>
    <p>No data found for <?php echo (int) $year; ?>.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Author</th>
                <th>Books in <?php echo (int) $year; ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $index => $row): ?>
            <tr>
                <td><?php echo (int) $index + 1; ?></td>
                <td><?php echo CHtml::encode($row['name']); ?></td>
                <td><?php echo (int) $row['books_count']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
