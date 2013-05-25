<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>类型管理</title>
    <style>
        body {
            font-size: 16px;
            text-align: center;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
            margin: 0 auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 0 1em; 
        }
    </style>
    <script src="/jquery-1.10.0.min.js"></script>
</head>
<body>
<button id="create_button">创建分类</button>
<table>
<tbody id="type_tbody">
<tr><th>类型ID</th><th>名称</th><th>创建时间</th><th></th></tr>
<?php foreach($types as $type) { ?>
<tr>
    <td><?= $type['id'] ?></td>
    <td><input type="text" origin="<?= $type['name'] ?>" value="<?= $type['name'] ?>" /></td>
    <td><?= $type['create_time'] ?></td>
    <td>
        <button class="save_button" disabled>保存</button>
        <button class="delete_button">删除</button>
    </td>
</tr>
<?php } ?>
</tbody>
</table>
<script id="empty_row" type="text/template">
<tr>
<td></td>
<td><input type="text" /></td>
<td></td>
<td>
<button class="save_button">保存</button>
<button class="delete_button">删除</button></td>
</tr>
</script>
<script>
    $(function(){
        $('#create_button').click(function(){
            $('#type_tbody').append($('#empty_row').html());
        });

        $('#type_tbody').on('click', 'button[class=save_button]', function(){
            var tr = $(this).parent('td').parent('tr');
            var value = $.trim(tr.children('td:nth-child(2) > input:text').val());
            if (value === '') { alert('类型名称不能为空'); return; };
            $.post(location.pathname, {
                'save': $.trim(tr.children('td:first-child').html()), 'name': value
            }, function(data, text_status, jq_xhr){
                console.log(data, text_status, jq_xhr);
            });
        }).on('click', 'button[class=delete_button]', function(){
            var tr = $(this).parent('td').parent('tr');
            var origin = tr.children('td:nth-child(2) > input:text').attr('origin')
            if(origin !== undefined || !confirm('确定删除: ' + origin)) return; 
            var id = $.trim(tr.children('td:first-child').html();
            if ( id === '') { tr.remove(); return; };
            $.post(location.pathname, {
                'delete': id 
            }, function(data, text_status, jq_xhr){
            });

        }).on( 'cut paste input keyup change', 'tr > td:nth-child(2) > input:text', function(e){
            var $this = $(this);
            var value = $.trim($this.val());
            //console.info($this.val(), e);
            $this.parent('td').siblings('td:last-child').children('button[class=save_button]')
                .attr('disabled', value === '' || value === $this.attr('origin'));
        });
        $('#tbody > tr > td:nth-child(2) > input:text').change();
    });
</script>
</body>
</html>
