<table>
<tbody>
<tr>
    <th>ID</th>
    <th>CID</th>
    <th>名称</th>
    <th>父CID</th>
    <th>is_parent</th>
    <th>爱折扣分类</th>
    <th>创建时间</th>
    <th>更新时间</th>
</tr>
<?php foreach($categories as $category) { ?>
<tr>
    <td><?= $category['id'] ?></td>
    <td><?= $category['cid'] ?></td>
    <td><?= $category['name'] ?></td>
    <td><?= $category['parent_cid'] ?></td>
    <td><?= $category['is_parent'] ? '是' : '' ?></td>
    <td>
        <select class="type_select">
<?php
$type_id = $category['type_id'];
if ($type_id > 0)
    $type_name = isset($types[$type_id]) ? $types[$type_id] : "未知ID: $type_id";
else $type_name = '';

echo <<<EOT
            <option value="$type_id">$type_name</option>
EOT;
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
echo paginate('/category_manage.do', $page, $total_count, $page_size);
?>
</div>

<script>
$(function(){
    $('#create_button').click(function(){
        $('#type_tbody').append($('#empty_row').html());
    });

    $('#type_tbody').on('click', 'button[class=save_button]', function(){
        var $this = $(this);
        var tr = $this.parent('td').parent('tr');
        var input = tr.find('td:nth-child(2) > input:text');
        var value = $.trim(input.val());
        if (value === '') { alert('类型名称不能为空'); return; };
        $.post(location.pathname, {
            'save': $.trim(tr.children('td:first-child').html()), 'name': value
        }, function(response){
            if (response.error){
                alert(response.error);
                return;
            }
            var data = response.data;
            tr.children('td:first-child').text(data.id);
            input.attr('origin', data.name);
            tr.children('td:nth-child(3)').text(data.create_time);
            tr.children('td:nth-child(4)').text(data.update_time || '');
            input.change();
            if($this.text() === '保存') $this.text('更新');
        }, 'json');
    }).on('click', 'button[class=delete_button]', function(){
        var tr = $(this).parent('td').parent('tr');
        var input = tr.find('td:nth-child(2) > input:text');
        var origin = $.trim(input.attr('origin'));
        if(origin){
            var val = input.val();
            var msg = '确定删除: ' + val;
            if(origin !== val) msg += ' 原名: ' + origin;
            if(!confirm(msg)) return;
        }
        var id = $.trim(tr.children('td:first-child').html());
        if ( id === '') { tr.remove(); return; };
        $.post(location.pathname, {
            'delete': id 
        }, function(data){
            if(data === 'ok') tr.remove();
            else alert(data);
        });
    }).on('cut paste input keyup change', 'tr > td:nth-child(2) > input:text', function(e){
        var $this = $(this);
        var value = $.trim($this.val());
        $this.parent('td').siblings('td:last-child').children('button[class=save_button]')
            .attr('disabled', value === '' || value === $this.attr('origin'));
    });
    $('#type_tbody > tr > td:nth-child(2) > input:text').change();
});
</script>
