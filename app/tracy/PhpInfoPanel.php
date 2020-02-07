<?php declare(strict_types = 1);

namespace App;

use Nette\Utils\Html;
use Tracy\IBarPanel;

class PhpInfoPanel implements IBarPanel
{

    public function getTab():string
    {
        return (string)Html::el('img')
            ->setAttribute('title', 'PhoInfo')
            ->src('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RkFENEExQTJEOTA5MTFFNzlBQUVGOEM1NThGM0EyMzIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RkFENEExQTNEOTA5MTFFNzlBQUVGOEM1NThGM0EyMzIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpGQUQ0QTFBMEQ5MDkxMUU3OUFBRUY4QzU1OEYzQTIzMiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpGQUQ0QTFBMUQ5MDkxMUU3OUFBRUY4QzU1OEYzQTIzMiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Ps9LvngAAAEFSURBVHja7NO9agJBEMDxVewEUYQUEgQrJVWCRCSFWCtYiAg2dpH4CGl8ALtUgSCIXSBBCCmsBMEXSCGWigQ/QIOkEAkhnv8lIyyHnYWNCz+4XXZmZ+fuHJZlqUOGUx04jp/AYZsXkEMYPvwgiG98YoBXPGNjBqawgGX4QBRT27r2h4wOdOEWT3JKDF6EcIYvFHEjQU2cI483lHWSiWStIS7PRamsjhGucIke2nItvW/pMq6hNw5xj7mcFJGgX/jxiK5UraRH6k6y6QYm0MBYerDCGhVcoIqW0YvS7vQsHjDb0zCtj44xXyJtf426cUlp5DUC8EiZbqnkBe/i/zs4/QtqK8AAtzFMV90opsIAAAAASUVORK5CYII=');
    }

    public function getPanel():string
    {

        ob_start();
        phpinfo();
        $data = ob_get_contents();
        ob_clean();

        return (string)Html::el('iframe')
            ->srcDoc($data)
            ->src('data:text/html,' . $data)
            ->style('overflow-y: scroll;min-height:500px;min-width:500px;resize:both;height:100%;width:100%;');
    }
}
