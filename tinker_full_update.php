DB::transaction(function() {

  if (!Schema::hasColumn('product_variations', 'image_url')) {
    Schema::table('product_variations', function($tbl) {
      $tbl->string('image_url', 500)->nullable()->after('barcode');
    });
    echo 'Added image_url column\n';
  } else { echo 'image_url already exists\n'; }

  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec 3x antiscratch%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC 3X Antiscratch - 45L Alloy Anti Scratch Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1vA9bDr1Kbty-ysOAOpMUViXQ6nUctbcj&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1vA9bDr1Kbty-ysOAOpMUViXQ6nUctbcj&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec ambassador%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Ambassador - 45L Alloy Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=15iNGpdx6z2alj4Dnqz6_z38UK4Jdld5Y&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=15iNGpdx6z2alj4Dnqz6_z38UK4Jdld5Y&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec black bird%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Black Bird - 45L Alloy Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1nqTDI-WzG3uAzDsNwUqBZInddUMxv5UL&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1nqTDI-WzG3uAzDsNwUqBZInddUMxv5UL&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec black dawn45l alloy dual lock top box with metal base plate%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Black Dawn45L Alloy Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1cdMzT1RMMN4AZNCZ55gYyPQGngCFWW-A&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1cdMzT1RMMN4AZNCZ55gYyPQGngCFWW-A&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec black warrior%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Black Warrior - 45L Alloy Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1eaM2wOLQRjJJUzDzGwRnfyXeI4nccsnp&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1eaM2wOLQRjJJUzDzGwRnfyXeI4nccsnp&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec dictator%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Dictator - 45L Alloy Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1Cmn9sDpKlFR-X99cnx2xsfIMTffgy-Mx&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['matte black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1Cmn9sDpKlFR-X99cnx2xsfIMTffgy-Mx&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec dictator anti scratch%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Dictator Anti Scratch - 45L Alloy Anti Scratch Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1n25Ifpl52G7c4OsYGvesH8mBpLadR48h&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1n25Ifpl52G7c4OsYGvesH8mBpLadR48h&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec extraction anti scratch%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Extraction Anti Scratch - 45L Alloy Anti Scratch Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=15fQWsJYN5SDX7g9zU5oNqpdJCeJxstag&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=15fQWsJYN5SDX7g9zU5oNqpdJCeJxstag&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%hexa moto%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'Hexa Moto - 45L Anti Scratch Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1nZLdbYbvCPlOtGzB_y4ivWVlH2CU491T&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1nZLdbYbvCPlOtGzB_y4ivWVlH2CU491T&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['grey'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1eQhm17gO9ryjtDgthML8p4tBquSS8DQK&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec intruders%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Intruders - 45L Alloy Anti Scratch 45L Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1yHLvjw2Z-8kC1GbNRpwWvebhoF1I8xoc&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1yHLvjw2Z-8kC1GbNRpwWvebhoF1I8xoc&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec tour%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Tour - 45L Alloy Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1eA81Bou5h_TR0AT98T-XW8u4jEw7-4yI&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1eA81Bou5h_TR0AT98T-XW8u4jEw7-4yI&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec ranger%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Ranger - 50L Alloy Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1Pq35LpiXjY6pLk4zORj1KfvayYKEBtBO&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1Pq35LpiXjY6pLk4zORj1KfvayYKEBtBO&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['silver'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1qcPAUTVUsRgsx71rWTUciu893qJZeoHD&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec classic%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Classic - 30L Hard Plastic Single Top Box', 'image_url' => 'https://drive.google.com/thumbnail?id=1492RybMG9VELqNMWPcTz_50W18EMLUTH&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1492RybMG9VELqNMWPcTz_50W18EMLUTH&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec black ghost%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Black Ghost - 38L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1RPUUp0detGxSoTlsx2ZwCcuiL919VAO9&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1RPUUp0detGxSoTlsx2ZwCcuiL919VAO9&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec 1x%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC 1X - 45L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1GnOHk7cwocaL4vbzrIfVOzH3CcxBQ6QF&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1GnOHk7cwocaL4vbzrIfVOzH3CcxBQ6QF&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec adamantium v2%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Adamantium v2 - 45L Hard Plastic Single Lock Top Box', 'image_url' => 'https://drive.google.com/thumbnail?id=1NWJzZYC4ftEMC6cQ3t7RjNoNjzlRSLdy&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1NWJzZYC4ftEMC6cQ3t7RjNoNjzlRSLdy&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec black ghost 2.0%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Black Ghost 2.0 - 45L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1RnuAvTgEQ82r4vBWp_NC8Ir5fk320PqL&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1RnuAvTgEQ82r4vBWp_NC8Ir5fk320PqL&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec black monster%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Black Monster - 45L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1y8hT3Ht6CpW4Gk1KpJBKOtmaWljG-m6y&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1y8hT3Ht6CpW4Gk1KpJBKOtmaWljG-m6y&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec x demon slayer gyomei%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC X Demon Slayer Gyomei - 45L Hard Plastic Single Lock Top Box', 'image_url' => 'https://drive.google.com/thumbnail?id=1QKwANEbc6thlxxdZU0wff-PHpU0zrTlU&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['yellow/red'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1QKwANEbc6thlxxdZU0wff-PHpU0zrTlU&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec x demon slayer nezuko%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC X Demon Slayer Nezuko - 45L Hard Plastic Single Lock Top Box', 'image_url' => 'https://drive.google.com/thumbnail?id=1YRZ4HIrFOnnK7-WLGio8XMZ107llV0bG&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['pink/green'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1YRZ4HIrFOnnK7-WLGio8XMZ107llV0bG&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec x demon slayer tanjiro%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC X Demon Slayer Tanjiro - 45L Hard Plastic Single Lock Top Box', 'image_url' => 'https://drive.google.com/thumbnail?id=1Pkf7SE2RpebgRIJSvXBgXS6YC_rjrugJ&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['green/black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1Pkf7SE2RpebgRIJSvXBgXS6YC_rjrugJ&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec x demon slayer wind hashira%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC X Demon Slayer Wind Hashira - 45L Hard Plastic Single Lock Top Box', 'image_url' => 'https://drive.google.com/thumbnail?id=1O9iCLgEoCVR5WmdLTbSgpLPVyPR_f64w&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white/green'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1O9iCLgEoCVR5WmdLTbSgpLPVyPR_f64w&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec freedom new%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Freedom New - 45L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1vHRGqWQyZzSC4Vif_YQ5YpvWHPUc3Gqo&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1vHRGqWQyZzSC4Vif_YQ5YpvWHPUc3Gqo&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec freedom old%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Freedom Old - 45L Hard Plasctic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1AExJ_vqjz_hxirwy4qjxB5V1JUP1BlrR&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1AExJ_vqjz_hxirwy4qjxB5V1JUP1BlrR&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%hnj coffer%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'HNJ Coffer - 45L Hard Plastic Single Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1jX14Y4hpb0FYSYJbnG6Kn783mm0vVBsc&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1jX14Y4hpb0FYSYJbnG6Kn783mm0vVBsc&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec legacy%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Legacy - 45L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1wNXXJnYq7zOvBA_g8F4iSIGgSHS80AY_&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1wNXXJnYq7zOvBA_g8F4iSIGgSHS80AY_&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec loot box number lock%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Loot Box Number Lock - 45L Hard Plastic Dual Lock Top Box', 'image_url' => 'https://drive.google.com/thumbnail?id=1nqyYEakzsDyOdgy1CJDYWtPgza_6lrcD&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1nqyYEakzsDyOdgy1CJDYWtPgza_6lrcD&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec loot box key lock%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Loot Box Key Lock - 45L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1QmWqB3zy18K8L9MXxko-BTFKuKimybmI&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1QmWqB3zy18K8L9MXxko-BTFKuKimybmI&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%mc ignite%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'MC Ignite - 45L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1GxNdiw8dg7lRru_JgBxfPopA4Rxs__e4&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1GxNdiw8dg7lRru_JgBxfPopA4Rxs__e4&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%mc graphene%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'MC Graphene - 45L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1lXaVqo9CyCh6_LXrP2Mr9ykURkMH7Zbj&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1lXaVqo9CyCh6_LXrP2Mr9ykURkMH7Zbj&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=11FyiMwG16-D5LnYuXCIU52sK-_CKnHDN&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec modern tech%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Modern Tech - 45L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1kweXnHz_muqjjeUWOj_4dWvilQm2Y3st&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1kweXnHz_muqjjeUWOj_4dWvilQm2Y3st&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec monster old%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Monster Old - 45L Hard Plastic Dual Lock Top Box wtih metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1Gt_cFFS2dJaSoNgJG5PuDnnL0NJq0oGZ&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1Gt_cFFS2dJaSoNgJG5PuDnnL0NJq0oGZ&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec ribs%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Ribs - 45L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1QveSfCUjJ8RlMK8z0x3Sz82MkcebSM9D&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1QveSfCUjJ8RlMK8z0x3Sz82MkcebSM9D&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1wHKWX4r6u6NbRH7Ti3yUWjD7ArtKiEo8&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec liberty%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Liberty - 60L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1GVIJNWNp4fsPpWrelp-8zCtRLmj97oQj&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1GVIJNWNp4fsPpWrelp-8zCtRLmj97oQj&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec secure%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Secure - 65L Hard Plastic Single Lock Top Box', 'image_url' => 'https://drive.google.com/thumbnail?id=12H7COD4UyvCKkrrzhH1Osi2ihew7pqq8&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=12H7COD4UyvCKkrrzhH1Osi2ihew7pqq8&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec hexalite%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Hexalite - 65L Hard Plastic Dual Lock Top Box with Metal Base Plate', 'image_url' => 'https://drive.google.com/thumbnail?id=1aioVA5ZsY8YV2OMirhgSlgXDG7kDZcS5&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1aioVA5ZsY8YV2OMirhgSlgXDG7kDZcS5&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec atmos%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Atmos - Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1nJzmaXSWEliM05HP-FhSd9vAnJrNoymx&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1nJzmaXSWEliM05HP-FhSd9vAnJrNoymx&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['blue'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1QUsY3kkjIBxlfteJqDu7qcWkVQnjIzTY&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec challenger%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Challenger - Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1YVl4IUq9ifvpxZplVfiH6DJXVes4A4s2&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1YVl4IUq9ifvpxZplVfiH6DJXVes4A4s2&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1SebCQwHQsHjbTgJCKz1JORxaslv0acFN&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec chroma%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Chroma - Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=12l6H2_3whHZ-A7jjuqR0dp6N2oVnhYJ9&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['matte black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=12l6H2_3whHZ-A7jjuqR0dp6N2oVnhYJ9&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1KRfoIDNpNNnxy39t48kN6PeoTx-1Z-UC&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec integra%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Integra - Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1l8p0tRoY9ycM5pJqzwdXmV8VAWeb29mX&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1l8p0tRoY9ycM5pJqzwdXmV8VAWeb29mX&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1MpuaM0IOGbR3010BiAUut5rKiqhV-LGQ&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['matte black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1kRQVx8quAnh3S9xQ-Wiqgry7o67J1mPG&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec pace%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Pace - Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=171qjC5ZMwaHsTCrojFJa70pKYsK8nmuC&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=171qjC5ZMwaHsTCrojFJa70pKYsK8nmuC&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1DfZwC8nuDNl2CCoJkeYpIuvL98klx0Xr&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['matte black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=11ccg7bNZtlAuBAnYZocKcru3tfOE-nAG&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec saga%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Saga - Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1hZv18lmlcJwR5DD9wiOfC7gyxeutVSBX&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black grey'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1hZv18lmlcJwR5DD9wiOfC7gyxeutVSBX&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black red'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1IPjrNlzeHwvUcfiQFljG9UBiWz4G2cfE&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black red grey'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1goR3cRTPn0YAijWegGl1dvVDRNqNWIwy&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['optic white grey'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1u5zkx1J8EmTDldfDbra97gWkfgqurX6S&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['plain gloss white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1qp0DLYRKw1FTciujxQ9_b7P_hOAQ0xcU&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['plain matte black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1vJp5tZfyHZuSLVV4ADRKN7fmMJ7VVWY9&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['silver black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=11dzZsxlrawsC9gr0kiiaxALJ6toARVj8&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec spots daily%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Spots Daily - Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1jo2u0i2QdOUKgg7Miody31ATO-2qbBP9&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['pink'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1jo2u0i2QdOUKgg7Miody31ATO-2qbBP9&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1ivGm-X7pMohEnXlybCDhcUF8h7tcl5me&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec x mesuca winnie the pooh%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC X MESUCA Winnie the Pooh - Disney Winnie the Pooh Theme Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1qprLLVqbcQhoKJ7NmwfDZBnYHXXFkzDT&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['yellow/white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1qprLLVqbcQhoKJ7NmwfDZBnYHXXFkzDT&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec zephyr%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Zephyr - Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1ko8KiuHy_-Sa7mjNHTTSaASQ-HGBaWRT&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss gray'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1ko8KiuHy_-Sa7mjNHTTSaASQ-HGBaWRT&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1vztOK0kKn1x7lXGxp6cPMPkZpU5S__fJ&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['matte black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1PRKhBDc-ABFt-jroCylYiMyJZPB_TWlG&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec essential%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Essential - Half Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1FhVgyRaPXQjnNGLD03EW2E1Z2Umc1YEd&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1FhVgyRaPXQjnNGLD03EW2E1Z2Umc1YEd&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=14jybu6mN0jFWqrzicQqLyZZI1-vOhvH-&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['matte black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1jrInMJYRbbOo6A0FAuw_UtVbcZaqsOIL&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec fade%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Fade - Half Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1dilSb8kHKFsHfelhLPUikQkCc25kg0n4&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1dilSb8kHKFsHfelhLPUikQkCc25kg0n4&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['matte black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1O4WveguvKvFEkQ1ho2WlzaKR2dQXR1Dx&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec focus%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Focus - Half Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=15YHsisa8HXH8F8BaizLV_V-kpioVFPl6&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=15YHsisa8HXH8F8BaizLV_V-kpioVFPl6&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white silver'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1V5ausG96F13fpTfFva5agcadc5Ew_5-E&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec grid duox%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Grid Duox - Half Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1D8uQhQYUEqEkIJVbWJWVUyaHReuegY60&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1D8uQhQYUEqEkIJVbWJWVUyaHReuegY60&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white red black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1WUScmhF_LYyuIo7hkulRj_zLmrVsvP3-&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec element%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Element - Modular (Half/Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1Ovv-6O_bR1Z0u4QoPjtaLAC9lVIXbKD_&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1Ovv-6O_bR1Z0u4QoPjtaLAC9lVIXbKD_&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['gloss white'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=19CpbhKkOSu7LTHXRcKedAyaN8VpWq1FS&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['matte black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=10Z8txTtTtUrGRGkaBw9N9pkhr2oskZKB&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec revolt air%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Revolt Air - Modular (Half/Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1kpUoUIP0u1rWPEXwqFtAHjdSXR537NQz&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['candy blue'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1kpUoUIP0u1rWPEXwqFtAHjdSXR537NQz&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['red chilli'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1d-Iq6ZQNTn9Qb8-xzmMRfJ9Rs2nD_LWo&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['royalty blue red'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1Es6_2TKn2ppCDcrK7zQuh57vX7mAZGTn&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['yellow lime'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1iSQ4aqfoHKLyFFga8ZzDsJhV5z8JPKPm&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec whirlwind pista%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Whirlwind Pista - Modular (Half/Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1vfvCyaNvDRaDSCVEA2eKqAUTPJBPH4bY&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['silver white black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1vfvCyaNvDRaDSCVEA2eKqAUTPJBPH4bY&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white black grey'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1JA4qZQzwuvwriykXwvIaenS6mCkNLBlF&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white blue red black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1fLWnUlJPSJNmrHvAlWySrvMkH_e793WS&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec whirlwind runner%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Whirlwind Runner - Modular (Half/Full Face Dual Visor Helmet', 'image_url' => 'https://drive.google.com/thumbnail?id=1jF7kO4laIEL1pa-f42eqN3xazDSItq3e&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black grey'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1jF7kO4laIEL1pa-f42eqN3xazDSItq3e&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black red'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1S-XLPV6Viezd_Sk7fG1gYHxSExE3ERB7&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['grey red'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1OqvrsnuHM8UvPHWxg6ckB2AT4CC9cEa-&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['white red'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1QBbw-Qarg1rKGXNwjErWOIfTLTjPSF-M&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%honda navi dc monorack%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'Honda Navi DC Monorack - Bracket for Honda Navi', 'image_url' => 'https://drive.google.com/thumbnail?id=1UPSSgwx0YVbVd3C_bw_Rv4E4mL4OGnoW&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1UPSSgwx0YVbVd3C_bw_Rv4E4mL4OGnoW&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%q4 rambo nmax v3%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'Q4 Rambo Nmax V3 - Bracket compatible for Nmax V3', 'image_url' => 'https://drive.google.com/thumbnail?id=1F0y-mbQ8_domkJgZ3-wo65WqBojo6DPZ&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1F0y-mbQ8_domkJgZ3-wo65WqBojo6DPZ&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%titan adv%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'Titan ADV - Bracket compatible for ADV 160', 'image_url' => 'https://drive.google.com/thumbnail?id=1O02PJpiV6RHypmfJ_StgacL7HlMsHdpj&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1O02PJpiV6RHypmfJ_StgacL7HlMsHdpj&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%metal warrior click%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'Metal Warrior Click - Bracket compatible for clink 125/150', 'image_url' => 'https://drive.google.com/thumbnail?id=1hIsN-JaaruJDsBkbwJDMToALR0qpF2or&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1hIsN-JaaruJDsBkbwJDMToALR0qpF2or&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%winner x bracket forward%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'Winner X Bracket Forward - Stay Grab Bracket Compatible for Winner X', 'image_url' => 'https://drive.google.com/thumbnail?id=1SIikKIxjaF4QNE2E7VX4LOUZlxSoMlgy&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1SIikKIxjaF4QNE2E7VX4LOUZlxSoMlgy&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%winner x bracket robin%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'Winner X Bracket Robin - Stay Grab Bracket Compatible for Winner X', 'image_url' => 'https://drive.google.com/thumbnail?id=1aGCA6XWz5Z4SveVKMm1tuCs2o4R1_sAo&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1aGCA6XWz5Z4SveVKMm1tuCs2o4R1_sAo&sz=w400']);
  }
  $pid = DB::table('products')->whereRaw('LOWER(product_name) LIKE ?', ['%sec q4 click%'])->value('product_id');
  if ($pid) {
    DB::table('products')->where('product_id', $pid)->update(['product_name' => 'SEC Q4 Click - Stay Grab Bracket Compatible for Honda Click', 'image_url' => 'https://drive.google.com/thumbnail?id=1L-PZ0v9-bIFq-uAe0JazNGOQFq-hENaM&sz=w400']);
    DB::table('product_variations')->where('product_id', $pid)->whereRaw('LOWER(variation_name) = ?', ['black'])->update(['image_url' => 'https://drive.google.com/thumbnail?id=1L-PZ0v9-bIFq-uAe0JazNGOQFq-hENaM&sz=w400']);
  }

});
echo 'Done!\n';