<table>
<tbody id="type_tbody">
<tr>
    <th>类型ID</th>
    <th>名称</th>
    <th>拼音</th>
    <th>创建时间</th>
    <th>更新时间</th>
    <th> <button id="create_button">新建</button> </th>
</tr>
<?php foreach($types as $type) { ?>
<tr>
    <td><?= $type['id'] ?></td>
    <td><input type="text" origin="<?= $type['name'] ?>" value="<?= $type['name'] ?>" autocomplete="off" /></td>
    <td><input type="text" origin="<?= $type['pinyin'] ?>" value="<?= $type['pinyin'] ?>" autocomplete="off" /></td>
    <td><?= $type['create_time'] ?></td>
    <td><?= $type['update_time'] ?></td>
    <td>
        <button class="save_button" disabled>更新</button>
        <button class="delete_button">删除</button>
    </td>
</tr>
<?php } ?>
</tbody>
</table>
<script id="empty_row" type="text/template">
<tr>
<td></td>
<td><input type="text" autocomplete="off" /></td>
<td><input type="text" autocomplete="off" /></td>
<td></td>
<td></td>
<td>
<button class="save_button" disabled>保存</button>
<button class="delete_button">删除</button>
</td>
</tr>
</script>
<script>
$(function(){
    $('#create_button').click(function(){
        $('#type_tbody > tr:first-child').after($('#empty_row').html());
    });

    $('#type_tbody').on('click', 'button[class=save_button]', function(){
        var $this = $(this);
        var tr = $this.parent('td').parent('tr');
        var name_input = tr.find('td:nth-child(2) > input:text');
        var pinyin_input = tr.find('td:nth-child(3) > input:text');
        var name = $.trim(name_input.val());
        var pinyin = $.trim(pinyin_input.val());
        if (name === '') { alert('类型名称不能为空'); return; };
        if (pinyin === '') { alert('类型拼音不能为空'); return; };
        $.post(location.pathname, {
            'save': $.trim(tr.children('td:first-child').html()), 'name': name, 'pinyin': pinyin
        }, function(response){
            if (response.error){
                alert(response.error);
                return;
            }
            var data = response.data;
            tr.children('td:first-child').text(data.id);
            name_input.attr('origin', data.name);
            pinyin_input.attr('origin', data.pinyin);
            tr.children('td:nth-child(4)').text(data.create_time);
            tr.children('td:nth-child(5)').text(data.update_time || '');
            name_input.change();
            pinyin_input.change();
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
    }).on('cut paste input keyup change', 'tr > td > input:text', function(e){
        var tr = $(this).parent('td').parent('tr');
        var name_input = tr.find('td:nth-child(2) > input:text');
        var pinyin_input = tr.find('td:nth-child(3) > input:text');
        var name, pinyin;
        
        var disabled = (name = $.trim(name_input.val())) === '' ||
            (pinyin = $.trim(pinyin_input.val())) === '' ||
            name === name_input.attr('origin') &&
            pinyin === pinyin_input.attr('origin');

        tr.children('td:last-child').children('button[class=save_button]').attr('disabled', disabled);
    });
    $('#type_tbody > tr > td > input:text').change();
});
</script>
