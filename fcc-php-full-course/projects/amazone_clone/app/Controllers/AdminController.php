<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\Review;
use App\Models\User;

class AdminController
{
    private const ORDER_STATUSES = ["pending", "paid", "shipped", "delivered", "cancelled"];
    private const RETURN_STATUSES = ["requested", "approved", "rejected", "received", "refunded"];

    public function __construct()
    {
        require_admin();
    }

    public function dashboard(): void
    {
        $pageTitle  = "Admin Dashboard";
        $stats      = [
            "products"  => Product::count(),
            "orders"    => Order::countAll(),
            "revenue"   => Order::revenue(),
            "low_stock" => count(Product::lowStock(10)),
            "returns"   => ReturnRequest::countByStatus("requested"),
        ];
        $recentOrders = array_slice(Order::adminAll(), 0, 8);
        $lowStock     = Product::lowStock(10);
        $salesChart   = Order::salesByDay(14);
        $topSellers   = Order::topSellers(5);
        $recentReturns = array_slice(ReturnRequest::adminAll(), 0, 5);
        $recentReviews = array_slice(Review::adminAll(), 0, 5);
        require __DIR__ . "/../views/admin/dashboard.php";
    }

    public function products(): void
    {
        $pageTitle = "Admin — Products";
        $q         = trim((string) ($_GET["q"] ?? ""));
        $products  = Product::adminAll($q);
        require __DIR__ . "/../views/admin/products.php";
    }

    public function newProduct(): void
    {
        $pageTitle   = "Admin — Add Product";
        $categories  = Product::categories();
        $old         = $_SESSION["old_product"] ?? null;
        unset($_SESSION["old_product"]);
        require __DIR__ . "/../views/admin/product_form.php";
    }

    public function createProduct(): void
    {
        csrf_verify();
        $input = $this->productInput();
        $error = $this->validateProduct($input);

        if ($error !== null) {
            $_SESSION["flash"]        = $error;
            $_SESSION["old_product"]  = $input;
            redirect("/admin/products/new");
        }
        $input["slug"] = slugify($input["slug"] !== "" ? $input["slug"] : $input["name"]);

        try {
            $input["image_url"] = upload_image($_FILES["image"] ?? null);
        } catch (\RuntimeException $e) {
            $_SESSION["flash"]       = $e->getMessage();
            $_SESSION["old_product"] = $input;
            redirect("/admin/products/new");
        }

        Product::createFrom($input);
        $_SESSION["flash"] = "Product added.";
        redirect("/admin/products");
    }

    public function editProduct(string $id): void
    {
        $product = Product::findById((int) $id);
        if ($product === null) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }

        $pageTitle  = "Admin — Edit Product";
        $categories = Product::categories();
        $old        = $_SESSION["old_product"] ?? null;
        unset($_SESSION["old_product"]);
        require __DIR__ . "/../views/admin/product_form.php";
    }

    public function updateProduct(string $id): void
    {
        csrf_verify();
        $product = Product::findById((int) $id);
        if ($product === null) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }

        $input = $this->productInput();
        $error = $this->validateProduct($input, (int) $id);

        if ($error !== null) {
            $_SESSION["flash"]       = $error;
            $_SESSION["old_product"] = $input;
            redirect("/admin/products/" . $id . "/edit");
        }
        $input["slug"] = slugify($input["slug"] !== "" ? $input["slug"] : $input["name"]);

        $input["image_url"] = $product["image_url"];
        try {
            $uploaded = upload_image($_FILES["image"] ?? null);
        } catch (\RuntimeException $e) {
            $_SESSION["flash"]       = $e->getMessage();
            $_SESSION["old_product"] = $input;
            redirect("/admin/products/" . $id . "/edit");
        }

        if ($uploaded !== null) {
            $this->removeImageFile($product["image_url"]);
            $input["image_url"] = $uploaded;
        }
        if (!empty($_POST["remove_image"]) && $product["image_url"] !== null) {
            $this->removeImageFile($product["image_url"]);
            $input["image_url"] = null;
        }

        Product::updateFrom((int) $id, $input);
        $_SESSION["flash"] = "Product updated.";
        redirect("/admin/products");
    }

    public function deleteProduct(string $id): void
    {
        csrf_verify();
        $product = Product::findById((int) $id);
        if ($product !== null) {
            $this->removeImageFile($product["image_url"]);
            Product::delete((int) $id);
            $_SESSION["flash"] = "Product deleted.";
        }
        redirect("/admin/products");
    }

    public function categories(): void
    {
        $pageTitle  = "Admin — Categories";
        $categories = Category::withCounts();
        require __DIR__ . "/../views/admin/categories.php";
    }

    public function createCategory(): void
    {
        csrf_verify();
        $name = trim((string) ($_POST["name"] ?? ""));
        $slug = slugify($name);

        if ($name === "" || $slug === "") {
            $_SESSION["flash"] = "Category needs a name.";
            redirect("/admin/categories");
        }

        Category::create($name, $slug);
        $_SESSION["flash"] = "Category added.";
        redirect("/admin/categories");
    }

    public function deleteCategory(string $id): void
    {
        csrf_verify();
        $category = Category::find((int) $id);
        if ($category !== null) {
            Category::delete((int) $id);
            $_SESSION["flash"] = "Category \"" . $category["name"] . "\" and its products were deleted.";
        }
        redirect("/admin/categories");
    }

    public function orders(): void
    {
        $pageTitle = "Admin — Orders";
        $status    = trim((string) ($_GET["status"] ?? ""));
        $q         = trim((string) ($_GET["q"] ?? ""));
        $orders    = Order::adminAll($status !== "" ? $status : null, $q);
        $statuses  = self::ORDER_STATUSES;
        require __DIR__ . "/../views/admin/orders.php";
    }

    public function orderDetail(string $id): void
    {
        $pageTitle = "Admin — Order #" . $id;
        $data      = Order::withItems((int) $id);
        if ($data === null) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }
        $statuses = self::ORDER_STATUSES;
        require __DIR__ . "/../views/admin/order_detail.php";
    }

    public function updateOrderStatus(string $id): void
    {
        csrf_verify();
        $status = (string) ($_POST["status"] ?? "");
        if (in_array($status, self::ORDER_STATUSES, true)) {
            Order::updateStatus((int) $id, $status);
            $_SESSION["flash"] = "Order #" . $id . " marked " . $status . ".";
        }
        redirect("/admin/orders/" . $id);
    }

    public function cancelOrder(string $id): void
    {
        csrf_verify();
        if (Order::cancel((int) $id)) {
            $_SESSION["flash"] = "Order #" . $id . " cancelled — stock was returned to inventory.";
        } else {
            $_SESSION["flash"] = "This order can't be cancelled (already shipped, delivered or cancelled).";
        }
        redirect("/admin/orders/" . $id);
    }

    public function returns(): void
    {
        $pageTitle = "Admin — Returns";
        $status    = trim((string) ($_GET["status"] ?? ""));
        $requests  = ReturnRequest::adminAll($status !== "" ? $status : null);
        $statuses  = self::RETURN_STATUSES;
        require __DIR__ . "/../views/admin/returns.php";
    }

    public function returnDetail(string $id): void
    {
        $pageTitle = "Admin — Return #" . $id;
        $data      = ReturnRequest::withItems((int) $id);
        if ($data === null) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }
        $statuses = self::RETURN_STATUSES;
        require __DIR__ . "/../views/admin/return_detail.php";
    }

    public function updateReturnStatus(string $id): void
    {
        csrf_verify();
        $returnId = (int) $id;
        $status   = (string) ($_POST["status"] ?? "");

        if (in_array($status, self::RETURN_STATUSES, true)) {
            $data = ReturnRequest::withItems($returnId);
            if ($data !== null) {
                // Items physically returned -> put them back in stock.
                if ($status === "received" && $data["request"]["status"] !== "received") {
                    ReturnRequest::restock($returnId);
                }
                ReturnRequest::updateStatus($returnId, $status);
                $_SESSION["flash"] = "Return #" . $returnId . " marked " . $status . ".";
            }
        }
        redirect("/admin/returns/" . $returnId);
    }

    public function users(): void
    {
        $pageTitle = "Admin — Users";
        $users     = User::adminAll();
        require __DIR__ . "/../views/admin/users.php";
    }

    public function toggleAdmin(string $id): void
    {
        csrf_verify();
        $target = User::find((int) $id);
        if ($target === null) {
            redirect("/admin/users");
        }
        // Never let an admin demote themselves.
        if ((int) $id === (int) current_user()["id"]) {
            $_SESSION["flash"] = "You can't change your own admin role.";
            redirect("/admin/users");
        }
        User::setAdmin((int) $id, !(bool) $target["is_admin"]);
        $_SESSION["flash"] = ($target["name"] ?? "User") . " is " . ((bool) $target["is_admin"] ? "no longer" : "now") . " an admin.";
        redirect("/admin/users");
    }

    public function reviews(): void
    {
        $pageTitle = "Admin — Reviews";
        $reviews   = Review::adminAll();
        require __DIR__ . "/../views/admin/reviews.php";
    }

    public function deleteReview(string $id): void
    {
        csrf_verify();
        Review::deleteById((int) $id);
        $_SESSION["flash"] = "Review removed and the product rating was recomputed.";
        redirect("/admin/reviews");
    }

    public function duplicateProduct(string $id): void
    {
        csrf_verify();
        Product::duplicate((int) $id);
        $_SESSION["flash"] = "Product duplicated (as a copy with 0 stock).";
        redirect("/admin/products");
    }

    // ---- private helpers ----------------------------------------------------

    private function productInput(): array
    {
        $deal = trim((string) ($_POST["deal_price"] ?? ""));
        return [
            "name"        => trim((string) ($_POST["name"] ?? "")),
            "slug"        => trim((string) ($_POST["slug"] ?? "")),
            "category_id" => (int) ($_POST["category_id"] ?? 0),
            "description" => trim((string) ($_POST["description"] ?? "")),
            "price"       => (float) ($_POST["price"] ?? 0),
            "deal_price"  => $deal === "" ? null : (float) $deal,
            "stock"       => (int) ($_POST["stock"] ?? 0),
        ];
    }

    private function validateProduct(array $input, ?int $ignoreId = null): ?string
    {
        if ($input["name"] === "") {
            return "Product needs a name.";
        }
        if (Category::find($input["category_id"]) === null) {
            return "Pick a category.";
        }
        if ($input["price"] < 0) {
            return "Price can't be negative.";
        }
        if ($input["stock"] < 0) {
            return "Stock can't be negative.";
        }
        if ($input["deal_price"] !== null && $input["deal_price"] >= $input["price"]) {
            return "The deal price must be lower than the regular price.";
        }

        $slug = slugify($input["slug"] !== "" ? $input["slug"] : $input["name"]);
        if ($slug === "") {
            return "A valid URL slug is needed.";
        }
        if (Product::slugExists($slug, $ignoreId)) {
            return "That URL slug is already used by another product.";
        }
        return null;
    }

    /** Best-effort cleanup of an uploaded image file. */
    private function removeImageFile(?string $url): void
    {
        if ($url === null || !str_starts_with($url, "/uploads/")) {
            return;
        }
        $file = dirname(__DIR__, 2) . "/public" . $url;
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
