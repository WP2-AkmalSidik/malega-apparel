import type { Metadata } from "next";
import { Plus_Jakarta_Sans } from "next/font/google";
import "./globals.css";
import { CartProvider } from "../context/CartContext";
import Navbar from "../components/Navbar";
import CartDrawer from "../components/CartDrawer";
import Footer from "../components/Footer";

const plusJakarta = Plus_Jakarta_Sans({
  subsets: ["latin"],
  weight: ["300", "400", "500", "600", "700", "800"],
  variable: "--font-plus-jakarta",
});

export const metadata: Metadata = {
  title: "MALEGA APPAREL | Bespoke Urban Streetwear & High-End Essentials",
  description: "Official E-Commerce Store of Malega Apparel. Discover Heavyweight 300GSM Boxy Tees, French Terry Hoodies, and Structured Streetwear silhouettes.",
  icons: {
    icon: "/favicon.ico"
  }
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="id" className={`${plusJakarta.variable} h-full antialiased`}>
      <body className="min-h-full flex flex-col bg-[#0B132B] text-[#FDFCFF] selection:bg-[#CBAC70] selection:text-[#0B132B]">
        <CartProvider>
          <Navbar />
          <main className="flex-1">
            {children}
          </main>
          <CartDrawer />
          <Footer />
        </CartProvider>
      </body>
    </html>
  );
}
