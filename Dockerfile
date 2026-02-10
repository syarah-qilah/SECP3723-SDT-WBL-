FROM php:8.2-cli

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy application files
COPY . /app
WORKDIR /app

# Expose port
EXPOSE 8080

# Start PHP server
CMD ["php", "-S", "0.0.0.0:8080", "-t", "."]