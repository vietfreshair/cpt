<?php

namespace Vietfreshair\Cpt;

class Cpt
{
    /**
     * Custom post type name/slug
     * 
     * @var string
     */
    protected $type;
    
    /**
     * @var string
     */
    protected $single;
    
    /**
     * @var string
     */
    protected $plural;
    
    /**
     * @var array
     */
    protected $args = [];
    
    /**
     * Constructor
     * 
     * @param string
     * @param string
     * @param string
     * @param array
     */
    public function __construct($type, $single = '', $plural = '', $args = [])
    {
        if (!is_string($type) || !is_string($single) || !is_string($plural)) {
            wp_die(__('It is required to pass a string.'));
        }
        
        if ($type == '') {
            wp_die(__('The name of custom post type is not empty.'));
        }
        
        $this->type = $type;
        $this->single = $single;
        $this->plural = $plural;
        $this->args = $args;
        
        add_action('init', [$this, 'registerPostType']);
    }
    
    /**
     * register CPT with merge arguments
     * 
     * @since 1.0.0
     */
    public function registerPostType()
    {
        $result = register_post_type($this->type, $this->getArgs());
        
        // check result return
        if (is_wp_error($result)) wp_die($result->get_error_message());
    }
    
    /**
     * handle and parse default arguments
     * 
     * @return array
     * @since  1.0.0
     */
    public function getArgs()
    {
        // generate cpt label
		$labels = array(
			'name'               => $this->plural,
			'singular_name'      => $this->single,
			'add_new'            => sprintf(__('Add New %s'), $this->single),
			'add_new_item'       => sprintf(__('Add New %s'), $this->single),
			'edit_item'          => sprintf(__('Edit %s'), $this->single),
			'new_item'           => sprintf(__('New %s'), $this->single),
			'all_items'          => sprintf(__('All %s'), $this->plural),
			'view_item'          => sprintf(__('View %s'), $this->single),
			'search_items'       => sprintf(__('Search %s'), $this->plural),
			'not_found'          => sprintf(__('No %s'), $this->plural),
			'not_found_in_trash' => sprintf(__('No %s found in Trash'), $this->plural),
			'parent_item_colon'  => isset($this->args['hierarchical']) && $this->args['hierarchical'] ? sprintf(__('Parent %s:'), $this->single ) : null,
			'menu_name'          => $this->plural,
			'insert_into_item'      => sprintf(__('Insert into %s'), strtolower($this->single)),
			'uploaded_to_this_item' => sprintf(__('Uploaded to this %s'), strtolower($this->single)),
			'items_list'            => sprintf(__('%s list'), $this->plural),
			'items_list_navigation' => sprintf(__('%s list navigation'), $this->plural),
			'filter_items_list'     => sprintf(__('Filter %s list'), strtolower($this->plural))
		);
		
		// Set default cpt parameters
		$defaults = array(
			'labels'             => [],
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'has_archive'        => true,
			'supports'           => ['title', 'editor', 'excerpt'],
		);
		
		$this->args = wp_parse_args($this->args, $defaults);
		$this->args['labels'] = wp_parse_args($this->args['label'], $labels);
		
		return $this->args;
    }
    
}