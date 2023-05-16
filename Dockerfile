# Use the official PHP image as the base
FROM php:7.4-cli

# Set the working directory inside the container
WORKDIR /app

# Copy the package files to the container
COPY . /app

# set composer allow super user
ENV COMPOSER_ALLOW_SUPERUSER 1

# Install git and zip
RUN apt-get update && apt-get install -y git zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install && composer dump-autoload -o


# Make the CLI entry point executable
RUN chmod +x bin/ector_cli

# Start the command-line interface and keep the container running
CMD ["tail", "-f", "/dev/null"]
