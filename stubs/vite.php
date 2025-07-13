<?php

/**
 * Vite helper stubs for IDE support
 */

if (!class_exists('Vite')) {
    class Vite
    {
        /**
         * Get asset URL with Vite manifest
         *
         * @param string $path
         * @return string
         */
        public static function asset(string $path): string
        {
            return '';
        }

        /**
         * Get asset with dependencies
         *
         * @param string $path
         * @return array
         */
        public static function assetWithDependencies(string $path): array
        {
            return [];
        }
    }
}
