<table>
<tbody id="category_tbody">
<tr>
    <th>ID</th>
    <th>CID</th>
    <th>名称</th>
    <th>父分类</th>
    <th>子分类</th>
    <th>兄弟分类</th>
    <th>爱折扣分类</th>
    <th>创建时间</th>
    <th>更新时间</th>
</tr>
<?php foreach($categories as $category) { ?>
<tr>
    <td><?= $category['id'] ?></td>
    <td><?= $category['cid'] ?></td>
    <td>
<?php if($category['parent_cid']) { ?>
        <a href="<?= "/category_manage.do?traceup={$category['cid']}" ?>"><?= $category['name'] ?></a>
<?php } else
    echo $category['name'];
?>
    </td>
    <td><?= (isset($category['parent_name']) ? $category['parent_name'] : '') .
    "({$category['parent_cid']})" ?></td>
    <td>
<?php if($category['is_parent']) { ?>
        <a href="<?= "/category_manage.do?parent={$category['cid']}" ?>">子分类</a>
<?php } ?>
    </td>
    <td>
        <a href="<?= "/category_manage.do?parent={$category['parent_cid']}" ?>">兄弟分类</a>
    </td>
    <td>
        <select class="type_select" autocomplete="off">
<?php
$type_id = $category['type_id'];
if ($type_id > 0)
{
    $type_name = isset($types[$type_id]) ? $types[$type_id] : "未知ID: $type_id";
    echo <<<EOT
            <option value="$type_id">$type_name</option>
EOT;
}
else $type_name = '';

echo '<option value="0"></option>
';
foreach($types as $key => $name) {
    if ($key != $type_id) echo <<<EOT
            <option value="$key">$name</option>
EOT;
}
?>
        </select>
    </td>
    <td><?= $category['create_time'] ?></td>
    <td><?= $category['update_time'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<div class="pagination">
<?php
require_once APP_ROOT . '/../common/helper/page.helper.php';
if (isset($parent) && $parent) $page_base = "/category_manage.do?parent=$parent&page=";
else $page_base = '/category_manage.do?page=';
echo paginate($page_base, '', $page, $total_count, $page_size);
?>
</div>

<script>
$(function(){
    $('select.type_select').change(function(){
        var $this = $(this);
        if($this.next('button.save_type').length <= 0)
            $this.after('<button class="save_type">保存</button>');
        $this.next('button.save_type').attr('disabled', false);
    });

    $('#category_tbody').on('click', 'button.save_type', function(){
        var $this = $(this);
        var td = $this.parent('td');
        $.post(location.pathname, {
            'update': $.trim(td.siblings('td:first-child').text()),
            'type_id': $.trim($this.siblings('select.type_select').val()),
        }, function(response){
            if(response.error) { alert(response.error); return; };
            td.siblings('td:last-child').text(response.data);
            $this.attr('disabled', true);
        }, 'json');
    });
});
</script>
