<?php
// Xử lý dữ liệu khi có file upload
$nodes = [];
$edges = [];
$urls = [];

if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, "r");
    $header = fgetcsv($handle); // Đọc dòng đầu tiên (header)

    while (($data = fgetcsv($handle)) !== FALSE) {
        // Map theo cấu trúc file inlinks.csv của bạn: From = index 1, To = index 2, Anchor = index 3
        $from = $data[1];
        $to = $data[2];
        $anchor = $data[3] ?? '';

        if (!in_array($from, $urls)) {
            $urls[] = $from;
            $nodes[] = ['id' => $from, 'label' => basename($from), 'title' => $from, 'color' => '#97c2fc'];
        }
        if (!in_array($to, $urls)) {
            $urls[] = $to;
            $nodes[] = ['id' => $to, 'label' => basename($to), 'title' => $to, 'color' => '#fb7e81', 'font' => ['bold' => true]];
        }

        $edges[] = [
            'from' => $from, 
            'to' => $to, 
            'label' => $anchor, 
            'arrows' => 'to',
            'font' => ['align' => 'top']
        ];
    }
    fclose($handle);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>SEO Inlinks Visualizer - Chuyên gia SEO</title>
    <script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    <style>
        body { font-family: sans-serif; margin: 20px; background: #f4f7f6; }
        #network-map { height: 600px; border: 1px solid #ddd; background: white; border-radius: 8px; }
        .controls { margin-bottom: 20px; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .node-info { margin-top: 10px; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>

    <h2>Công cụ Trực quan hóa Inlinks cho SEO Expert</h2>
    
    <div class="controls">
        <form action="" method="post" enctype="multipart/form-data">
            <label>Chọn file CSV (Inlinks): </label>
            <input type="file" name="csv_file" accept=".csv" required>
            <button type="submit">Phân tích dữ liệu</button>
        </form>
        <p class="node-info"><i>* Mẹo: Nút màu đỏ là trang đích (Target), nút màu xanh là trang nguồn (Source).</i></p>
    </div>

    <div id="network-map"></div>

    <script type="text/javascript">
        // Nhận dữ liệu từ PHP
        const nodesData = <?php echo json_encode($nodes); ?>;
        const edgesData = <?php echo json_encode($edges); ?>;

        if (nodesData.length > 0) {
            const container = document.getElementById('network-map');
            const data = {
                nodes: new vis.DataSet(nodesData),
                edges: new vis.DataSet(edgesData)
            };
            const options = {
                nodes: {
                    shape: 'dot',
                    size: 16,
                    font: { size: 12 }
                },
                edges: {
                    width: 2,
                    color: { inherit: 'from' },
                    smooth: { type: 'continuous' }
                },
                physics: {
                    forceAtlas2Based: {
                        gravitationalConstant: -26,
                        centralGravity: 0.005,
                        springLength: 230,
                        springConstant: 0.18
                    },
                    maxVelocity: 146,
                    solver: 'forceAtlas2Based',
                    timestep: 0.35,
                    stabilization: { iterations: 150 }
                }
            };
            const network = new vis.Network(container, data, options);
        } else {
            document.getElementById('network-map').innerHTML = "<p style='padding:20px'>Vui lòng upload file để hiển thị bản đồ.</p>";
        }
    </script>
</body>
</html>