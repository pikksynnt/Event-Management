import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";
import { jwtVerify } from "jose";

const COOKIE_NAME = "session_token";

async function verifyToken(token: string) {
  try {
    const secret = process.env.SESSION_SECRET;
    if (!secret) return null;
    const encodedSecret = new TextEncoder().encode(secret);
    const { payload } = await jwtVerify(token, encodedSecret, {
      algorithms: ["HS256"],
    });
    return payload;
  } catch {
    return null;
  }
}

export async function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl;
  const token = request.cookies.get(COOKIE_NAME)?.value;

  // Protect Event Manager dashboard routes (Layer 1 Authorization)
  if (pathname.startsWith("/dashboard/manager")) {
    if (!token) {
      const loginUrl = new URL("/login", request.url);
      return NextResponse.redirect(loginUrl);
    }

    const payload = await verifyToken(token);
    if (!payload || payload.role !== "event_manager") {
      const loginUrl = new URL("/login", request.url);
      const response = NextResponse.redirect(loginUrl);
      // Clean invalid cookie if present
      response.cookies.delete(COOKIE_NAME);
      return response;
    }

    return NextResponse.next();
  }

  // Redirect authenticated event_manager away from login page
  if (pathname === "/login") {
    if (token) {
      const payload = await verifyToken(token);
      if (payload && payload.role === "event_manager") {
        return NextResponse.redirect(new URL("/dashboard/manager", request.url));
      }
    }
    return NextResponse.next();
  }

  return NextResponse.next();
}

export const config = {
  matcher: ["/dashboard/manager/:path*", "/login"],
};
