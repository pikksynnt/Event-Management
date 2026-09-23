"use server";

import prisma from "@/lib/prisma";
import bcrypt from "bcryptjs";
import { createSession, destroySession } from "@/lib/auth";

export interface ActionState {
  success?: boolean;
  error?: string;
}

export async function loginAction(
  _prevState: ActionState | null,
  formData: FormData
): Promise<ActionState> {
  try {
    const email = formData.get("email")?.toString().trim();
    const password = formData.get("password")?.toString();

    if (!email || !password) {
      return { error: "Email dan password wajib diisi." };
    }

    const user = await prisma.user.findUnique({
      where: { email },
    });

    if (!user) {
      return { error: "Email atau password tidak valid." };
    }

    const isMatch = await bcrypt.compare(password, user.passwordHash);
    if (!isMatch) {
      return { error: "Email atau password tidak valid." };
    }

    // Role verification
    if (user.role !== "event_manager") {
      return { error: "Akses ditolak. Akun bukan Event Manager." };
    }

    // Create session cookie
    await createSession({
      id: user.id,
      email: user.email,
      role: user.role,
      name: user.name,
    });

    return { success: true };
  } catch (error) {
    console.error("Login error:", error);
    return { error: "Terjadi kesalahan pada server. Silakan coba lagi." };
  }
}

export async function logoutAction(): Promise<void> {
  await destroySession();
}
