<? foreach($items['items'] as &$item): ?>
	<div class="item" id="item_<?= $item['id'] ?>">
		<span class="date"><?= rudate('d M Y', $item['date']) ?></span>
        <button onclick="setItemRead('<?= $item['id'] ?>')">Отметить прочитанным</button>
		<h2><a href="<?= h($item['href']) ?>" target="_blank"><?= h($item['title']) ?></a></h2>

		<?= h2($item['description']) ?>
	</div>
<? endforeach; ?>
