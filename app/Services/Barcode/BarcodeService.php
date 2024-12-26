// PENDIENTE
    // public function getProductById($producto_Id)
    // {
    //     $this->authenticate();

    //     $producto = $this->models->execute_kw(
    //         $this->db,
    //         $this->uid,
    //         $this->password,
    //         'product.template',
    //         'search_read',
    //         [
    //             [['id', '=', $producto_Id]]
    //         ],
    //         [
    //             'fields' => ['id', 'name', 'list_price', 'categ_id', 'barcode', 'default_code', 'attribute_line_ids'],
    //             'limit' => 1
    //         ]
    //     );

    //     if (!is_array($producto) || empty($producto)) {
    //         return null;
    //     }

    //     $producto = $producto[0];

    //     $resultado = [
    //         'id' => $producto['id'],
    //         'name' => $producto['name'],
    //         'list_price' => $producto['list_price'],
    //         'categ_id' => is_array($producto['categ_id']) ? $producto['categ_id'][1] : $producto['categ_id'],
    //         'barcode' => $producto['barcode'],
    //         'default_code' => $producto['default_code'],
    //         'attribute_line_ids' => $producto['attribute_line_ids'],
    //         'variants' => []
    //     ];

    //     if (!empty($producto['attribute_line_ids'])) {
    //         $variantes = $this->models->execute_kw(
    //             $this->db,
    //             $this->uid,
    //             $this->password,
    //             'product.product',
    //             'search_read',
    //             [
    //                 [['product_tmpl_id', '=', $producto_Id]]
    //             ],
    //             [
    //                 'fields' => ['id', 'name', 'list_price', 'barcode', 'default_code']
    //             ]
    //         );

    //         $resultado['variants'] = $variantes;
    //     }

    //     return $resultado;
    // }
