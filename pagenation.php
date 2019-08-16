<div class="pagenation">
            <ul>
              <li class="pagenation-list list-wide"><a class="pagenation-char"  href="index.php?p=1<?php echo appendGetParam(array('p'));?>">&lt;&lt; 最初</a></li>
              <?php for($i = $minPageNum; $i <= $maxPageNum; $i++){?>
              
                <?php if($i !== $currentPageNum){ ?>
                  <li class="pagenation-list">
                <a class="pagenation-char" href="index.php?p=<?php echo $i . appendGetParam(array('p'));?>"> <?php echo $i;?></a>
                <?php }else{ ?>
                  <li class="pagenation-list pagenation-active">
                  <a class="pagenation-char-acive"> <?php echo $i;?></a>
                  </li>
                <?php } ?>
              
              <?php } ?>
              <li class="pagenation-list list-wide"><a class="pagenation-char" href="index.php?p=<?php echo $dbDrawingData['total_page'] . appendGetParam(array('p'));?>">最後 &gt;&gt;</a></li>
              
            </ul>
          </div>