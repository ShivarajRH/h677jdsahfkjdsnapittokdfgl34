<div class="container">
    <h2>Edit Streams</h2>
    <table width="100%" class="datagrid datagridsort">
        <tr>
            <th>Id</th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Created Time</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($streams as $stream): ?>
        <tr>
            <td><?=$stream['id']?></td>
            <td><?=$stream['title']?></td>
            <td><?=$stream['description']?></td>
            <td><?=$stream['status']?></td>
            <td><?=ucfirst($stream['username'])?></td>
            <td><abbr class="timeago" title="<?=date("Y-m-d H:i:s",$stream['created_time'])?>"><?=date("Y-m-d H:i:s",$stream['created_time'])?></abbr></td>
            <td><a href="<?=site_url("admin/stream_edit/").'/'.$stream['id']?>">Edit</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<style type="text/css">
    .leftcont {    display:none; }
    .grid_bg { background:#F1F0FF; }

    .datagrid th { background: #443266;color: #C3C3E5; }
</style>
<script type="text/javascript">
    $("abbr.timeago").timeago();
</script>
