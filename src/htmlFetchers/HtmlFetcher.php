<?php

namespace mipotech\criticalcss\htmlFetchers;

use yii\base\Component;
use yii\helpers\Url;

use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
/**
 * This implementation fetches HTML for a given URI by mocking a Request and
 * letting a new instance of the Application handle it.
 */
class HtmlFetcher extends Component
{
    /**
     * {@inheritdoc}
     */
    public function fetch($uri)
    {
        $response = $this->call($uri);

        if (!$response) {
            throw new HtmlFetchingException(
                sprintf('Invalid response from URI [%s].', $uri['uri'])
            );
        }
        return $this->stripCss($response);
    }
    /**
     * Remove any existing inlined critical-path CSS that has been generated
     * previously. Old '<style>' tags should be tagged with a `data-inline`
     * attribute.
     *
     * @param  string $html
     *
     * @return string
     */
    protected function stripCss($html)
    {
        return preg_replace('/\<style data-inlined\>.*\<\/style\>/s', '', $html);
    }
    /**
     * Call the given URI and return a Response.
     *
     * @param  string $uri
     *
     * @return \Illuminate\Http\Response
     */
    protected function call($uri)
    {
        return file_get_contents($uri['uri']);
    }
}