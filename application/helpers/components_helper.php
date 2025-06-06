<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Components helper for Tailwind CSS

/**
 * Generate a button with Tailwind classes
 */
if (!function_exists('btn')) {
    function btn($text, $href = '#', $type = 'primary', $size = 'md', $classes = '') {
        $base_classes = 'inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';
        
        $sizes = [
            'sm' => 'px-3 py-2 text-sm',
            'md' => 'px-4 py-2 text-sm',
            'lg' => 'px-6 py-3 text-base'
        ];
        
        $types = [
            'primary' => 'bg-primary hover:bg-primary-dark text-white focus:ring-primary',
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500',
            'danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
            'success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
            'outline' => 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 focus:ring-primary'
        ];
        
        $button_classes = $base_classes . ' ' . $sizes[$size] . ' ' . $types[$type] . ' ' . $classes;
        
        return '<a href="' . $href . '" class="' . $button_classes . '">' . $text . '</a>';
    }
}

/**
 * Generate a card with Tailwind classes
 */
if (!function_exists('card')) {
    function card($content, $classes = '') {
        $card_classes = 'bg-white shadow-sm border border-gray-200 rounded-lg p-6 ' . $classes;
        return '<div class="' . $card_classes . '">' . $content . '</div>';
    }
}

/**
 * Generate a badge/tag with Tailwind classes
 */
if (!function_exists('badge')) {
    function badge($text, $color = 'blue', $classes = '') {
        $badge_classes = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-' . $color . '-100 text-' . $color . '-800 ' . $classes;
        return '<span class="' . $badge_classes . '">' . $text . '</span>';
    }
}

/**
 * Generate a form input with Tailwind classes
 */
if (!function_exists('form_input_tw')) {
    function form_input_tw($name, $type = 'text', $value = '', $placeholder = '', $classes = '') {
        $input_classes = 'block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary ' . $classes;
        
        if ($type === 'textarea') {
            return '<textarea name="' . $name . '" class="' . $input_classes . '" placeholder="' . $placeholder . '" rows="4">' . $value . '</textarea>';
        }
        
        return '<input type="' . $type . '" name="' . $name . '" value="' . $value . '" placeholder="' . $placeholder . '" class="' . $input_classes . '">';
    }
}

/**
 * Generate a complete input field with label, input, and optional help text
 */
if (!function_exists('input_field')) {
    function input_field($config = []) {
        $defaults = [
            'type' => 'text',
            'name' => '',
            'id' => '',
            'label' => '',
            'value' => '',
            'placeholder' => '',
            'required' => false,
            'disabled' => false,
            'readonly' => false,
            'help_text' => '',
            'error' => '',
            'wrapper_class' => 'mb-4',
            'label_class' => '',
            'input_class' => '',
            'rows' => 4,
            'options' => [], // For select inputs
            'multiple' => false, // For select inputs
            'attributes' => []
        ];
        
        $config = array_merge($defaults, $config);
        
        // Generate unique ID if not provided
        if (empty($config['id'])) {
            $config['id'] = $config['name'];
        }
        
        $html = '<div class="' . $config['wrapper_class'] . '">';
        
        // Label
        if (!empty($config['label'])) {
            $required_mark = $config['required'] ? ' <span class="text-red-500">*</span>' : '';
            $label_classes = 'block text-sm font-medium text-gray-700 mb-1 ' . $config['label_class'];
            $html .= '<label for="' . $config['id'] . '" class="' . $label_classes . '">' . $config['label'] . $required_mark . '</label>';
        }
        
        // Build input classes
        $input_classes = 'block w-full rounded-md border px-3 py-2 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-1 transition-colors ' . $config['input_class'];
        
        // Add error or normal border classes
        if (!empty($config['error'])) {
            $input_classes .= ' border-red-300 focus:border-red-500 focus:ring-red-500';
        } else {
            $input_classes .= ' border-gray-300 focus:border-primary focus:ring-primary';
        }
        
        // Add disabled/readonly classes
        if ($config['disabled']) {
            $input_classes .= ' bg-gray-50 cursor-not-allowed';
        }
        
        // Build attributes string
        $attributes = [];
        $attributes[] = 'name="' . $config['name'] . '"';
        $attributes[] = 'id="' . $config['id'] . '"';
        $attributes[] = 'class="' . $input_classes . '"';
        
        if (!empty($config['placeholder'])) {
            $attributes[] = 'placeholder="' . htmlspecialchars($config['placeholder']) . '"';
        }
        
        if ($config['required']) {
            $attributes[] = 'required';
        }
        
        if ($config['disabled']) {
            $attributes[] = 'disabled';
        }
        
        if ($config['readonly']) {
            $attributes[] = 'readonly';
        }
        
        // Add custom attributes
        foreach ($config['attributes'] as $attr => $attr_value) {
            if ($attr_value === true) {
                $attributes[] = $attr;
            } else {
                $attributes[] = $attr . '="' . htmlspecialchars($attr_value) . '"';
            }
        }
        
        $attributes_string = implode(' ', $attributes);
        
        // Generate input based on type
        switch ($config['type']) {
            case 'textarea':
                $html .= '<textarea ' . $attributes_string . ' rows="' . $config['rows'] . '">' . htmlspecialchars($config['value']) . '</textarea>';
                break;
                
            case 'select':
                $html .= '<select ' . $attributes_string . ($config['multiple'] ? ' multiple' : '') . '>';
                foreach ($config['options'] as $option_value => $option_text) {
                    $selected = '';
                    if ($config['multiple'] && is_array($config['value'])) {
                        $selected = in_array($option_value, $config['value']) ? ' selected' : '';
                    } else {
                        $selected = ($option_value == $config['value']) ? ' selected' : '';
                    }
                    $html .= '<option value="' . htmlspecialchars($option_value) . '"' . $selected . '>' . htmlspecialchars($option_text) . '</option>';
                }
                $html .= '</select>';
                break;
                
            case 'checkbox':
                $checked = $config['value'] ? ' checked' : '';
                $html .= '<div class="flex items-center">';
                $html .= '<input type="checkbox" ' . $attributes_string . ' value="1"' . $checked . ' class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">';
                if (!empty($config['label'])) {
                    $html .= '<label for="' . $config['id'] . '" class="ml-2 block text-sm text-gray-900">' . $config['label'] . '</label>';
                }
                $html .= '</div>';
                break;
                
            case 'radio':
                foreach ($config['options'] as $option_value => $option_text) {
                    $checked = ($option_value == $config['value']) ? ' checked' : '';
                    $radio_id = $config['id'] . '_' . $option_value;
                    $html .= '<div class="flex items-center mb-2">';
                    $html .= '<input type="radio" name="' . $config['name'] . '" id="' . $radio_id . '" value="' . htmlspecialchars($option_value) . '"' . $checked . ' class="h-4 w-4 text-primary focus:ring-primary border-gray-300">';
                    $html .= '<label for="' . $radio_id . '" class="ml-2 block text-sm text-gray-900">' . htmlspecialchars($option_text) . '</label>';
                    $html .= '</div>';
                }
                break;
                
            default:
                $html .= '<input type="' . $config['type'] . '" ' . $attributes_string . ' value="' . htmlspecialchars($config['value']) . '">';
                break;
        }
        
        // Help text
        if (!empty($config['help_text'])) {
            $html .= '<p class="mt-1 text-sm text-gray-500">' . $config['help_text'] . '</p>';
        }
        
        // Error message
        if (!empty($config['error'])) {
            $html .= '<p class="mt-1 text-sm text-red-600">' . $config['error'] . '</p>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

/**
 * Generate a simple text input field
 */
if (!function_exists('text_input')) {
    function text_input($name, $label = '', $value = '', $placeholder = '', $required = false, $attributes = []) {
        return input_field([
            'type' => 'text',
            'name' => $name,
            'label' => $label,
            'value' => $value,
            'placeholder' => $placeholder,
            'required' => $required,
            'attributes' => $attributes
        ]);
    }
}

/**
 * Generate an email input field
 */
if (!function_exists('email_input')) {
    function email_input($name, $label = '', $value = '', $placeholder = '', $required = false, $attributes = []) {
        return input_field([
            'type' => 'email',
            'name' => $name,
            'label' => $label,
            'value' => $value,
            'placeholder' => $placeholder ?: 'Enter email address',
            'required' => $required,
            'attributes' => $attributes
        ]);
    }
}

/**
 * Generate a password input field
 */
if (!function_exists('password_input')) {
    function password_input($name, $label = '', $placeholder = '', $required = false, $attributes = []) {
        return input_field([
            'type' => 'password',
            'name' => $name,
            'label' => $label,
            'placeholder' => $placeholder ?: 'Enter password',
            'required' => $required,
            'attributes' => $attributes
        ]);
    }
}

/**
 * Generate a textarea field
 */
if (!function_exists('textarea_input')) {
    function textarea_input($name, $label = '', $value = '', $placeholder = '', $rows = 4, $required = false, $attributes = []) {
        return input_field([
            'type' => 'textarea',
            'name' => $name,
            'label' => $label,
            'value' => $value,
            'placeholder' => $placeholder,
            'rows' => $rows,
            'required' => $required,
            'attributes' => $attributes
        ]);
    }
}

/**
 * Generate a select dropdown field
 */
if (!function_exists('select_input')) {
    function select_input($name, $label = '', $options = [], $value = '', $required = false, $multiple = false, $attributes = []) {
        return input_field([
            'type' => 'select',
            'name' => $name,
            'label' => $label,
            'options' => $options,
            'value' => $value,
            'required' => $required,
            'multiple' => $multiple,
            'attributes' => $attributes
        ]);
    }
}

/**
 * Generate a checkbox field
 */
if (!function_exists('checkbox_input')) {
    function checkbox_input($name, $label = '', $checked = false, $attributes = []) {
        return input_field([
            'type' => 'checkbox',
            'name' => $name,
            'label' => $label,
            'value' => $checked,
            'attributes' => $attributes,
            'wrapper_class' => 'mb-4'
        ]);
    }
}

/**
 * Generate radio button fields
 */
if (!function_exists('radio_input')) {
    function radio_input($name, $label = '', $options = [], $value = '', $required = false, $attributes = []) {
        return input_field([
            'type' => 'radio',
            'name' => $name,
            'label' => $label,
            'options' => $options,
            'value' => $value,
            'required' => $required,
            'attributes' => $attributes
        ]);
    }
}

/**
 * Generate a form label with Tailwind classes
 */
if (!function_exists('form_label_tw')) {
    function form_label_tw($text, $for = '', $required = false) {
        $label_classes = 'block text-sm font-medium text-gray-700 mb-1';
        $required_text = $required ? ' <span class="text-red-500">*</span>' : '';
        
        return '<label for="' . $for . '" class="' . $label_classes . '">' . $text . $required_text . '</label>';
    }
}

/**
 * Generate a form group (label + input)
 */
if (!function_exists('form_group_tw')) {
    function form_group_tw($label, $name, $type = 'text', $value = '', $placeholder = '', $required = false) {
        $html = '<div class="mb-4">';
        $html .= form_label_tw($label, $name, $required);
        $html .= form_input_tw($name, $type, $value, $placeholder);
        $html .= '</div>';
        
        return $html;
    }
}

/**
 * Generate an alert/notification with Tailwind classes
 */
if (!function_exists('alert')) {
    function alert($message, $type = 'info', $dismissible = false) {
        $types = [
            'success' => 'bg-green-50 border-green-200 text-green-800',
            'error' => 'bg-red-50 border-red-200 text-red-800',
            'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'info' => 'bg-blue-50 border-blue-200 text-blue-800'
        ];
        
        $alert_classes = 'border rounded-md p-4 ' . $types[$type];
        $dismiss_btn = $dismissible ? '<button type="button" class="float-right text-gray-400 hover:text-gray-600" onclick="this.parentElement.remove()">×</button>' : '';
        
        return '<div class="' . $alert_classes . '">' . $dismiss_btn . $message . '</div>';
    }
}

/**
 * Generate a pagination component
 */
if (!function_exists('pagination_tw')) {
    function pagination_tw($current_page, $total_pages, $base_url) {
        if ($total_pages <= 1) return '';
        
        $html = '<nav class="flex justify-center mt-8">';
        $html .= '<div class="flex space-x-1">';
        
        // Previous button
        if ($current_page > 1) {
            $html .= '<a href="' . $base_url . '?page=' . ($current_page - 1) . '" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">Previous</a>';
        }
        
        // Page numbers
        for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) {
            $active_class = $i == $current_page ? 'bg-primary text-white' : 'bg-white text-gray-500 hover:bg-gray-50';
            $html .= '<a href="' . $base_url . '?page=' . $i . '" class="px-3 py-2 text-sm font-medium border border-gray-300 ' . $active_class . '">' . $i . '</a>';
        }
        
        // Next button
        if ($current_page < $total_pages) {
            $html .= '<a href="' . $base_url . '?page=' . ($current_page + 1) . '" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">Next</a>';
        }
        
        $html .= '</div>';
        $html .= '</nav>';
        
        return $html;
    }
}

/**
 * Generate a status badge
 */
if (!function_exists('status_badge')) {
    function status_badge($status) {
        $statuses = [
            'published' => ['text' => 'Published', 'class' => 'bg-green-100 text-green-800'],
            'draft' => ['text' => 'Draft', 'class' => 'bg-yellow-100 text-yellow-800'],
            'archived' => ['text' => 'Archived', 'class' => 'bg-gray-100 text-gray-800']
        ];
        
        $status_info = $statuses[$status] ?? ['text' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'];
        
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $status_info['class'] . '">' . $status_info['text'] . '</span>';
    }
}

/**
 * Generate a post meta section
 */
if (!function_exists('post_meta')) {
    function post_meta($author, $date, $status = null, $tags = []) {
        $html = '<div class="flex flex-wrap items-center text-sm text-gray-500 space-x-4 mb-4">';
        
        $html .= '<span class="flex items-center">';
        $html .= '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">';
        $html .= '<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>';
        $html .= '</svg>';
        $html .= 'By ' . $author;
        $html .= '</span>';
        
        $html .= '<span class="flex items-center">';
        $html .= '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">';
        $html .= '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>';
        $html .= '</svg>';
        $html .= date('M d, Y', strtotime($date));
        $html .= '</span>';
        
        if ($status) {
            $html .= '<span>' . status_badge($status) . '</span>';
        }
        
        $html .= '</div>';
        
        if (!empty($tags)) {
            $html .= '<div class="flex flex-wrap gap-2 mb-4">';
            foreach ($tags as $tag) {
                $color = isset($tag['color']) ? $tag['color'] : '#007cba';
                $html .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background-color: ' . $color . '">';
                $html .= $tag['name'];
                $html .= '</span>';
            }
            $html .= '</div>';
        }
        
        return $html;
    }
}

/**
 * Generate a search form
 */
if (!function_exists('search_form')) {
    function search_form($current_search = '', $action = '') {
        $html = '<form method="GET" action="' . $action . '" class="bg-gray-50 p-6 rounded-lg mb-6">';
        $html .= '<div class="flex flex-col sm:flex-row gap-4">';
        $html .= '<div class="flex-1">';
        $html .= '<input type="text" name="search" value="' . htmlspecialchars($current_search) . '" placeholder="Search posts..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary">';
        $html .= '</div>';
        $html .= '<button type="submit" class="px-6 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors">Search</button>';
        $html .= '</div>';
        
        if ($current_search) {
            $html .= '<div class="mt-4">';
            $html .= '<a href="' . $action . '" class="text-sm text-gray-500 hover:text-gray-700">Clear search</a>';
            $html .= '</div>';
        }
        
        $html .= '</form>';
        
        return $html;
    }
} 
