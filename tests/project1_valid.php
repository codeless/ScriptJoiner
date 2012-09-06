<?php
# Comment 1
echo 'File 1', PHP_EOL;
// Comment 2
echo 'File 2', PHP_EOL;
echo 'File 3', PHP_EOL;
class Class1
{
    public function foo()
    {
        # Comment
        echo 'File 4', PHP_EOL;;
        if (1) {
            if (1) {
                if (1) {
                    # Comment
                    echo 'File 5', PHP_EOL;;
                } else {
                    # Comment
                    echo 'File 6', PHP_EOL;;
                }
            } else {
                # Comment
                echo 'File 6', PHP_EOL;;
            }
        } else {
            # Comment
            echo 'File 6', PHP_EOL;;
        }
    }
};;;