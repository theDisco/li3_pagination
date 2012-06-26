<?php

namespace li3_pagination\extensions\helper;

/**
 * Helper can be used to render pagination for a binding.
 */
class Pagination extends \lithium\template\Helper {

    /**
     * String templates used by this helper.
     *
     * @var array
     */
    protected $_strings = array(
        'start' => '<ul>',
        'item' => '<li{:options}>{:page}</li>',
        'page' => '<a href="{:url}">{:label}</a>',
        'end'   => '</ul>',
    );

    /**
     * An array holding initial options passed to create method.
     *
     * @var array
     */
    protected $_options = array();

    /**
     * List of controls supported by this helper.
     *
     * @var array
     */
    protected $_controls = array(
        'first',
        'previous',
        'next',
        'last'
    );

    /**
     * Map of classes used by this helper.
     *
     * @var array
     */
    protected $_classes = array(
        'router' => 'lithium\net\http\Router',
    );

    /**
     * Method has to be executed as first. It sets up the options and config for
     * scope of current pagination.
     *
     * @param $binding
     * @param array $options
     * @return string|\li3_pagination\extensions\helper\Pagination
     * @filter
     */
    public function create($binding, array $options = array()) {
        $model = $binding->model();
        $class = $model::invokeMethod('_object');
        $count = $class::count();

        $defaults = array(
            'page' => 1,
            'limit' => 20,
            'count' => $count,
            'start' => true
        );

        $this->_options = $options + $defaults;
        $this->_options += array('pages' => intval(ceil($this->_options['count'] / $this->_options['limit'])));
        $this->_config();

        if ($this->_options['start']) {
            return $this->start();
        }

        return $this;
    }

    /**
     * Proxy to controls.
     *
     * @param $method
     * @param $args
     * @return string
     */
    public function __call($method, $args) {
        return $this->controls($method, $args);
    }

    /**
     * Helper method used for hiding navigation with one page
     *
     * @return integer
     */
    public function count() {
        return $this->_options['pages'];
    }

    /**
     * Helper method used for starting pagination.
     *
     * @return string
     */
    public function start() {
        return $this->_render(__METHOD__, 'start', array('options' => ''));
    }

    /**
     * Method renders pages for current binding.
     *
     * @param array $options
     * @return string
     * @filter
     */
    public function pages(array $options = array()) {
        $default = array(
            'type' => 'sliding',
            'range' => 10,
            'page' => $this->_options['page']
        );
        $options = $options + $default;

        $router = $this->_classes['router'];
        $request = $this->_context->request();
        $extra = array('method' => __METHOD__);
        $params = compact('options');

        $filter = function($self, $params) use ($router, $request, $extra) {
            switch ($params['options']['type']) {
                default:
                    $pages = $self->invokeMethod('_sliding', array($params['options']['range']));
                    break;
            }

            $options = array();
            $_pages = array();
            foreach ($pages as $_page) {
                unset($options['class']);
                if ($_page == $params['options']['page']) {
                    $options['class'] = 'active';
                }

                $url = $router::match(array('page' => $_page) + $request->params, $request);
                $label = $_page;

                $page = $self->invokeMethod('_render', array($extra['method'], 'page', compact('url', 'label')));
                $_pages[] = $self->invokeMethod('_render', array($extra['method'], 'item', compact('page', 'options')));
            }

            return implode('', $_pages);
        };
        return $this->_filter(__METHOD__, $params, $filter);
    }

    /**
     * Clean up the options and config and render the html tag.
     *
     * @return string
     */
    public function end() {
        unset($this->_options);
        unset($this->_config);
        return $this->_context->strings('end');
    }

    /**
     * Method renders pagination controls surrounding the pagination.
     *
     * @param $type
     * @param array $options
     * @return string
     * @throws \BadMethodCallException
     * @filter
     */
    public function controls($type, array $options = array()) {
        if (!in_array($type, $this->_controls)) {
            throw new \BadMethodCallException('Only following types of controls are supported: ' . implode(', ', $this->_controls));
        }

        $defaults = array(
            'label' => $this->_config[$type]['label'],
            'url' => $this->_config[$type]['url'],
            'page' => $this->_config[$type]['page'],
        );

        $options = array('_controls' => $this->_controls, '_options' => $this->_options) + $options + $defaults;
        $extra = array('method' => __METHOD__);
        $params = compact('options');

        $filter = function($self, $params) use ($type, $extra) {
            $url = $params['options']['url'];
            $label = $params['options']['label'];
            $page = $params['options']['page'];

            $options = array();
            if (
                in_array($type, array_slice($params['options']['_controls'], 0, 2)) && intval($params['options']['_options']['page']) === $page
                ||
                in_array($type, array_slice($params['options']['_controls'], 2, 2)) && intval($params['options']['_options']['page']) === $page
            ) {
                $options['class'] = 'disabled';
            }

            $page = $self->invokeMethod('_render', array($extra['method'], 'page', compact('url', 'label')));
            return $self->invokeMethod('_render', array($extra['method'], 'item', compact('page', 'options')));
        };
        return $this->_filter(__METHOD__, $params, $filter);
    }

    /**
     * Method calculates the controls for current pagination.
     *
     * @return void
     */
    protected function _config() {
        $router = $this->_classes['router'];
        $request = $this->_context->request();
        $params = $this->_context->request()->params;

        $first = array(
            'label' => '&larr;',
            'page' => 1,
            'url' => $router::match(array('page' => 1) + $params, $request),
        );

        $_last = $this->_options['pages'];
        $last = array(
            'label' => '&rarr;',
            'page' => $_last,
            'url' => $router::match(array('page' => $_last) + $params, $request)
        );

        $_previous = (intval($this->_options['page']) === 1) ? 1 : intval($this->_options['page']) - 1;
        $previous = array(
            'label' => '&laquo;',
            'page' => $_previous,
            'url' => $router::match(array('page' => $_previous) + $params, $request)
        );

        $_next = (intval($this->_options['page']) === $_last) ? $_last : intval($this->_options['page']) + 1;
        $next = array(
            'label' => '&raquo;',
            'page' => $_next,
            'url' => $router::match(array('page' => $_next) + $params, $request)
        );

        $this->_config = compact('first', 'last', 'previous', 'next');
    }

    /**
     * Method calculates the pages for a sliding navigation.
     *
     * @param $range
     * @return array
     */
    protected function _sliding($range) {
        $pages = $this->_options['pages'];
        $page = $this->_options['page'];

        if ($range > $pages) {
            $range = $pages;
        }

        $delta = ceil($range / 2);

        if ($page - $delta > $pages - $range) {
            $lower = $pages - $range + 1;
            $upper = $pages;
        } else {
            if ($page - $delta < 0) {
                $delta = $page;
            }

            $offset = $page - $delta;
            $lower = $offset + 1;
            $upper = $offset + $range;
        }

        $tmp = array();

        for ($page = $lower; $page <= $upper; $page++) {
            $tmp[] = $page;
        }

        return $tmp;
    }
}