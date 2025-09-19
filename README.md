# LatePoint Bulk Services Addon

A powerful WordPress plugin addon that extends LatePoint's functionality by allowing administrators to add multiple services in bulk to orders with advanced scheduling options and category filtering.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0+-green.svg)
![LatePoint](https://img.shields.io/badge/LatePoint-Required-orange.svg)
![License](https://img.shields.io/badge/license-GPL--2.0+-red.svg)

![image](screenshots/video.mp4)

## 🚀 Features

### ✨ Core Functionality
- **Bulk Service Addition**: Add multiple services to orders simultaneously with a single click
- **Native Integration**: Seamlessly integrates with LatePoint's existing order management interface
- **Category Filtering**: Filter services by categories for easier selection in large service catalogs
- **Real-time Updates**: Instant price and duration calculations as services are selected
- **Permission-based Access**: Respects LatePoint's user roles and permissions system

### 🎯 User Experience
- **Intuitive Interface**: Clean, native-looking interface that matches LatePoint's design system
- **Service Categories**: Organize and filter services by categories including "All Categories" and "Uncategorized"
- **Visual Feedback**: Clear visual indicators for selected services and loading states
- **Responsive Design**: Works seamlessly on desktop and mobile devices
- **Accessibility**: Proper form labels and keyboard navigation support

### 🔧 Technical Features
- **Clean Code Architecture**: Follows WordPress and LatePoint coding standards
- **Translation Ready**: Full internationalization support with text domain
- **Performance Optimized**: Lightweight implementation with minimal overhead
- **Error Handling**: Comprehensive validation and error reporting
- **AJAX Integration**: Smooth, non-blocking service addition process

## 📋 Requirements

- **WordPress**: 5.0 or higher
- **LatePoint**: Latest version (required)
- **PHP**: 7.4 or higher
- **User Permissions**: `booking__create` capability required for bulk operations

## 🛠 Installation

### Method 1: Manual Installation

1. **Download** the plugin files
2. **Upload** the `latepoint-bulk-services` folder to your `/wp-content/plugins/` directory
3. **Activate** the plugin through the 'Plugins' menu in WordPress
4. **Ensure** LatePoint is installed and activated

### Method 2: WordPress Admin

1. Go to **Plugins > Add New**
2. **Upload** the plugin zip file
3. **Install** and **Activate** the plugin
4. **Verify** LatePoint is active

### Verification

After installation, you should see:
- A "Bulk Add" button in the LatePoint order management interface
- The addon listed in LatePoint's installed addons section

## 📖 Usage

### Adding Bulk Services to Orders

1. **Navigate** to LatePoint > Orders
2. **Edit** an existing order or create a new one
3. **Click** the "Bulk Add" button next to "Add Another Item"
4. **Filter** services by category (if multiple categories exist)
5. **Select** the services you want to add by clicking on them
6. **Click** "Add Selected Services" to add them to the order

### Category Filtering

The addon automatically detects your service categories and provides filtering options:
- **All Categories**: Shows all available services
- **Specific Categories**: Shows only services in the selected category
- **Uncategorized**: Shows services without assigned categories (if any exist)

### Service Information Display

Each service shows:
- **Service Name**: Clear identification
- **Duration**: Time required in minutes
- **Price**: Formatted according to your LatePoint settings
- **Category**: Service category (if assigned)

## 🏗 File Structure

```
latepoint-bulk-services/
├── latepoint-bulk-services.php     # Main plugin file
├── README.md                       # This documentation
├── languages/                      # Translation files
│   └── empty.md
├── lib/                           # Core functionality
│   ├── controllers/
│   │   └── bulk_services_controller.php
│   ├── helpers/
│   │   └── bulk_services_helper.php
│   └── views/
│       └── bulk_services/
│           └── get_selection_interface.php
└── public/                        # Frontend assets
    ├── javascripts/
    │   └── bulk-services-admin.js
    └── stylesheets/
        └── bulk-services-admin.css
```

## 🔌 Hooks & Filters

### Actions
- `latepoint_order_quick_edit_form_content_after` - Adds bulk services interface to order forms
- `latepoint_admin_enqueue_scripts` - Loads admin scripts and styles

### Filters
- `latepoint_installed_addons` - Registers the addon with LatePoint

### Custom Functions

#### Helper Functions
```php
// Get available services
OsBulkServicesHelper::get_available_services()

// Get service categories
OsBulkServicesHelper::get_service_categories()

// Check if bulk services can be added
OsBulkServicesHelper::can_add_bulk_services($order)

// Validate selected services
OsBulkServicesHelper::validate_selected_services($services, $customer_id)
```

## 🎨 Customization

### Styling
The addon uses LatePoint's native styling system. To customize the appearance:

1. **Override CSS** in your theme:
```css
.bulk-services-wrapper {
    /* Your custom styles */
}

.bulk-service-connection {
    /* Service item styling */
}
```

2. **Modify** the CSS file directly (not recommended for updates):
```
public/stylesheets/bulk-services-admin.css
```

### Functionality
Extend functionality by hooking into the addon's actions:

```php
// Custom validation
add_filter('bulk_services_validate_selection', 'my_custom_validation');

// Modify service display
add_filter('bulk_services_format_service', 'my_service_formatter');
```

## 🌐 Translation

The addon is translation-ready. To translate:

1. **Create** a `.po` file for your language in the `languages/` directory
2. **Use** the text domain: `latepoint-bulk-services`
3. **Translate** all strings marked with `__()`, `_e()`, `esc_html_e()`, etc.

### Available Strings
- Interface labels and buttons
- Error messages and validation text
- Category filter options
- Status messages

## 🐛 Troubleshooting

### Common Issues

**Bulk Add button not appearing:**
- Verify LatePoint is active and up to date
- Check user permissions (`booking__create` capability)
- Ensure multiple items are allowed in LatePoint settings

**Services not loading:**
- Confirm services are set to "Active" status
- Check if services have proper pricing configured
- Verify database connectivity

**Category filter not showing:**
- Ensure you have multiple service categories created
- Check that categories have active services assigned

**JavaScript errors:**
- Clear browser cache
- Check for plugin conflicts
- Verify jQuery is loaded

### Debug Mode
Enable WordPress debug mode to see detailed error messages:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

### Development Setup
1. **Clone** the repository
2. **Install** WordPress and LatePoint in a local environment
3. **Activate** the addon for testing
4. **Follow** WordPress coding standards

### Code Standards
- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- Use proper sanitization and validation
- Include inline documentation
- Write meaningful commit messages

### Pull Request Process
1. **Fork** the repository
2. **Create** a feature branch
3. **Make** your changes with proper testing
4. **Submit** a pull request with detailed description

### Reporting Issues
- Use the GitHub issue tracker
- Provide detailed reproduction steps
- Include WordPress and LatePoint version information
- Add relevant error messages or screenshots

## 📄 License

This project is licensed under the GPL-2.0+ License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**KNYN.DEV**
- Website: [https://knyn.dev/](https://knyn.dev/)
- LatePoint: [https://latepoint.dev](https://latepoint.dev)

## 🙏 Acknowledgments

- **LatePoint Team** for creating an excellent booking system
- **WordPress Community** for the robust plugin architecture
- **Contributors** who help improve this addon

## 📞 Support

For support and questions:
- **Documentation**: Check this README and LatePoint documentation
- **Issues**: Use the GitHub issue tracker
- **Community**: LatePoint community forums

---

**Made with ❤️ for the LatePoint community**
