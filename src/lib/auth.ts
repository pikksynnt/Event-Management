import { SignJWT, jwtVerify } from "jose";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";

export interface SessionPayload {
  id: number;
  email: string;
  role: string;
  name: string;
}

const COOKIE_NAME = "session_token";

function getSecretKey() {
  const secret = process.env.SESSION_SECRET;
  if (!secret || secret.length < 32) {
    throw new Error("SESSION_SECRET must be at least 32 characters long");
  }
  return new TextEncoder().encode(secret);
}

/**
 * Sign payload into encrypted JWT string
 */
export async function signSession(payload: SessionPayload): Promise<string> {
  const secret = getSecretKey();
  return new SignJWT({ ...payload })
    .setProtectedHeader({ alg: "HS256" })
    .setIssuedAt()
    .setExpirationTime("7d")
    .sign(secret);
}

/**
 * Verify and decode JWT string
 */
export async function verifySession(token: string): Promise<SessionPayload | null> {
  try {
    const secret = getSecretKey();
    const { payload } = await jwtVerify(token, secret, {
      algorithms: ["HS256"],
    });

    return {
      id: Number(payload.id),
      email: String(payload.email),
      role: String(payload.role),
      name: String(payload.name),
    };
  } catch {
    return null;
  }
}

/**
 * Create session cookie (httpOnly)
 */
export async function createSession(payload: SessionPayload): Promise<void> {
  const token = await signSession(payload);
  const cookieStore = await cookies();

  cookieStore.set(COOKIE_NAME, token, {
    httpOnly: true,
    secure: process.env.NODE_ENV === "production",
    sameSite: "lax",
    path: "/",
    maxAge: 60 * 60 * 24 * 7, // 7 days
  });
}

/**
 * Get current session from cookie
 */
export async function getSession(): Promise<SessionPayload | null> {
  const cookieStore = await cookies();
  const token = cookieStore.get(COOKIE_NAME)?.value;

  if (!token) {
    return null;
  }

  return await verifySession(token);
}

/**
 * Clear session cookie and redirect to login
 */
export async function destroySession(): Promise<void> {
  const cookieStore = await cookies();
  cookieStore.delete(COOKIE_NAME);
  redirect("/login");
}

/**
 * Server-side authorization helper:
 * Requires authenticated session. Redirects to /login if missing.
 */
export async function requireAuth(): Promise<SessionPayload> {
  const session = await getSession();
  if (!session) {
    redirect("/login");
  }
  return session;
}

/**
 * Server-side authorization helper:
 * Requires user with role "event_manager".
 * Redirects to /login if missing or role doesn't match.
 */
export async function requireEventManager(): Promise<SessionPayload> {
  const session = await requireAuth();
  if (session.role !== "event_manager") {
    redirect("/login");
  }
  return session;
}
