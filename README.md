WebRestPro 🍽️

Demo SaaS for restaurants
A PHP (Slim Framework) project that allows restaurants to manage their digital menus and customers to view them in real time through a QR code.

✨ Current Features
👤 User Management (Admin Panel)

Login/logout via REST API (Slim).

Roles: admin and normal.

User CRUD (create, edit, delete, list).

Admin view with cards and modal dialogs for actions.

Links from users → list of restaurants associated to that user.

🍴 Restaurants

Each user can own one or more restaurants.

Admin panel: list restaurants belonging to a user.

Restaurant detail page with:

Restaurant information (address, phone, etc.).

List of dishes associated to the restaurant.

🥗 Dishes

CRUD for dishes linked to a restaurant.

Attributes: name, description, allergens, full/half price, food type.

📲 Dynamic QR Codes

QR code generation in PHP (using phpqrcode
).

Each QR points to a restaurant’s public menu.

Example: qr.php?id=5 → QR for the menu of restaurant with ID=5.

🖥️ Interface

Modern, responsive CSS design (dark/light mode, cards, tables, modals).

Icons: Remix Icons
 via CDN.

Hybrid flow:

Multi-page navigation (gest_usuarios.php, usuario_restaurantes.php, restaurante_detalle.php).

Quick actions (create, edit, delete) with modals and fetch/AJAX.

⚙️ Technologies

Backend: PHP 8.x, Slim Framework
.

Database: MySQL (tables: user, restaurant, page_web_content).

Frontend: HTML5, CSS3, vanilla JavaScript.

QR: PHP QR Code (phpqrcode library).

Local server: XAMPP (Apache + MySQL).

📂 Project Structure
/admin
 ├── gest_usuarios.php         # User management
 ├── usuario_restaurantes.php  # Restaurants by user
 ├── restaurante_detalle.php   # Restaurant detail + dishes
/public
 ├── carta.php                 # Public restaurant menu
 ├── qr.php                    # Dynamic QR code generator
/src
 ├── funciones.php             # Database logic (users, restaurants, dishes)
 ├── seguridad.php             # Session security middleware
 ├── funciones_ctes.php        # Config + helpers
 └── bd_config.php             # DB config
/servicios_rest
 └── index.php                 # Slim endpoints (users, restaurants, dishes)

🚧 To-Do / Next Steps

Migrate passwords to password_hash() / password_verify() (currently plain text).

Add CSRF protection to forms.

Improve public menu UI (responsive, elegant).

Filtering and searching in user/restaurant/dish lists.

Deployment setup for production hosting.
