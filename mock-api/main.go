package main

import (
	"encoding/base64"
	"log"
	"strconv"
	"strings"
	"time"

	"github.com/gofiber/fiber/v2"
)

type PurchaseRequest struct {
	Receipt string `json:"receipt"`
}

type VerifyRequest struct {
	Receipt string `json:"receipt"`
}

type PurchaseResponse struct {
	Status     bool   `json:"status"`
	ExpireDate string `json:"expire_date"`
}

type VerifyResponse struct {
	Status bool `json:"status"`
}

type ErrorResponse struct {
	Status  bool   `json:"status"`
	Message string `json:"message"`
}

func createExpireDate(isValid bool) string {
	utcMinusSixZone := time.FixedZone("UTC-6", -6*60*60)
	if !isValid {
		return time.Now().In(utcMinusSixZone).Format("2006-01-02 15:04:05")
	}

	return time.Now().AddDate(0, 0, 30).In(utcMinusSixZone).Format("2006-01-02 15:04:05")
}

func validateReceipt(receipt string) (bool, error) {
	if receipt == "" {
		return false, fiber.NewError(fiber.StatusBadRequest, "Receipt is required")
	}

	if len(receipt) < 2 {
		return false, fiber.NewError(fiber.StatusBadRequest, "Receipt must be at least 2 characters long")
	}

	lastTwoChars := receipt[len(receipt)-2:]
	lastTwoDigits, err := strconv.Atoi(lastTwoChars)

	if err != nil {
		return false, fiber.NewError(fiber.StatusBadRequest, "Invalid receipt")
	}

	if isDivisibleBySix(lastTwoDigits) {
		return false, fiber.NewError(fiber.StatusTooManyRequests, "Rate limit exceeded")
	}

	return isOddNumber(lastTwoDigits), nil
}

func isOddNumber(number int) bool {
	return number%2 == 1
}

func isDivisibleBySix(num int) bool {
	return num%6 == 0
}

func createErrorResponse(c *fiber.Ctx, err error) error {
	if ferr, ok := err.(*fiber.Error); ok {
		return c.Status(ferr.Code).JSON(ErrorResponse{
			Status:  false,
			Message: ferr.Message,
		})
	}
	return c.Status(fiber.StatusInternalServerError).JSON(ErrorResponse{
		Status:  false,
		Message: "Internal server error",
	})
}

func handlePurchaseRequest(c *fiber.Ctx) error {
	req := new(PurchaseRequest)
	if err := c.BodyParser(req); err != nil {
		return createErrorResponse(c, fiber.NewError(fiber.StatusBadRequest, "Invalid request"))
	}

	isValid, err := validateReceipt(req.Receipt)
	if err != nil {
		return createErrorResponse(c, err)
	}

	return c.JSON(PurchaseResponse{
		Status:     isValid,
		ExpireDate: createExpireDate(isValid),
	})
}

func handleVerifyRequest(c *fiber.Ctx) error {
	req := new(VerifyRequest)
	if err := c.BodyParser(req); err != nil {
		return createErrorResponse(c, fiber.NewError(fiber.StatusBadRequest, "Invalid request"))
	}

	_, err := validateReceipt(req.Receipt)
	if err != nil {
		return createErrorResponse(c, err)
	}

	return c.JSON(VerifyResponse{
		Status: true,
	})
}

func basicAuthHandler(c *fiber.Ctx) error {
	authHeader := c.Request().Header.Peek("Authorization")
	if authHeader == nil {
		return c.SendStatus(fiber.StatusUnauthorized)
	}

	authHeaderStr := string(authHeader)
	if !strings.HasPrefix(authHeaderStr, "Basic ") {
		return c.SendStatus(fiber.StatusUnauthorized)
	}

	authHeaderStr = strings.TrimPrefix(authHeaderStr, "Basic ")
	decoded, err := base64.StdEncoding.DecodeString(authHeaderStr)
	if err != nil {
		return c.SendStatus(fiber.StatusUnauthorized)
	}

	username, password, ok := strings.Cut(string(decoded), ":")
	if !ok {
		return c.SendStatus(fiber.StatusUnauthorized)
	}

	if username != "admin" || password != "password" {
		return c.SendStatus(fiber.StatusUnauthorized)
	}

	return c.Next()
}

func setupRoutes(app *fiber.App) {
	google := app.Group("/google")
	google.Post("/purchase", basicAuthHandler, handlePurchaseRequest)
	google.Post("/verify", basicAuthHandler, handleVerifyRequest)

	ios := app.Group("/ios")
	ios.Post("/purchase", basicAuthHandler, handlePurchaseRequest)
	ios.Post("/verify", basicAuthHandler, handleVerifyRequest)

	callback := app.Group("/callback")
	callback.Post("/", func(c *fiber.Ctx) error {
		return c.JSON(fiber.Map{
			"status": true,
		})
	})
}

func main() {
	app := fiber.New(fiber.Config{
		AppName: "Subscription Mock API",
	})

	setupRoutes(app)

	log.Fatal(app.Listen(":3000"))
}
