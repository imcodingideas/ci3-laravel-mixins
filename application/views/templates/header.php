<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'CodeIgniter Blog'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#007cba',
                        'primary-dark': '#005a87',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans leading-relaxed">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-gray-800 text-white shadow-lg">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center space-x-6">
                        <a href="<?php echo base_url(); ?>" class="text-xl font-bold hover:text-blue-300 transition-colors">
                            CodeIgniter Blog
                        </a>
                        <div class="hidden md:flex space-x-4">
                            <a href="<?php echo base_url(); ?>" class="hover:text-blue-300 transition-colors">Home</a>
                            <a href="<?php echo base_url('create'); ?>" class="hover:text-blue-300 transition-colors">Create Post</a>
                            <a href="<?php echo base_url('authors'); ?>" class="hover:text-blue-300 transition-colors">Authors</a>
                            <a href="<?php echo base_url('tags'); ?>" class="hover:text-blue-300 transition-colors">Tags</a>
                        </div>
                    </div>
                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button type="button" class="text-gray-300 hover:text-white" onclick="toggleMobileMenu()">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- Mobile menu -->
                <div id="mobile-menu" class="hidden md:hidden pb-4">
                    <div class="flex flex-col space-y-2">
                        <a href="<?php echo base_url(); ?>" class="hover:text-blue-300 transition-colors">Home</a>
                        <a href="<?php echo base_url('create'); ?>" class="hover:text-blue-300 transition-colors">Create Post</a>
                        <a href="<?php echo base_url('authors'); ?>" class="hover:text-blue-300 transition-colors">Authors</a>
                        <a href="<?php echo base_url('tags'); ?>" class="hover:text-blue-300 transition-colors">Tags</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Container -->
        <main class="flex-1">
            <div class="max-w-6xl mx-auto px-4 py-8">
                <!-- Page Title -->
                <?php if (isset($title) && $title !== 'CodeIgniter Blog'): ?>
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900"><?php echo $title; ?></h1>
                    </div>
                <?php endif; ?>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
